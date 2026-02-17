<?php

namespace app\services;

use app\repository\AchatRepository;
use app\repository\BesoinRepository;
use app\repository\DonRepository;
use Exception;
use Flight;

class DonService
{
  private $donRepository;

  public function __construct(DonRepository $donRepository)
  {
    $this->donRepository = $donRepository;
  }

  public function getAllTypesBesoin()
  {
    return $this->donRepository->getAllTypesBesoin();
  }

  public function getBesoinsDisponibles()
  {
    return $this->donRepository->getBesoinsDisponibles();
  }

  public function getAllTypesBesoinForDonForm()
  {
    return $this->donRepository->getAllTypesBesoinForDonForm();
  }

  public function dispatchDons(string $mode = 'date')
  {
    if ($mode === 'quantity') {
      return $this->executeDispatchByQuantity(false);
    }
    if ($mode === 'proportional') {
      return $this->executeDispatchByProportional(false);
    }
    return $this->executeDispatchByDate(false); // Mode par défaut: FIFO par date
  }

  /**
   * Simule le dispatch des dons sans enregistrer dans la base de données
   */
  public function simulateDispatch(string $mode = 'date')
  {
    if ($mode === 'quantity') {
      return $this->executeDispatchByQuantity(true);
    }
    if ($mode === 'proportional') {
      return $this->executeDispatchByProportional(true);
    }
    return $this->executeDispatchByDate(true); // Mode par défaut: FIFO par date
  }

  /**
   * Exécute ou simule le dispatch des dons en mode FIFO par date
   * LOGIQUE:
   * - Les dons sont matchés par libellé exact avec les besoins (ex: don "Riz" → besoin "Riz")
   * - Pour l'argent, libelle = NULL pour les deux
   * - Besoins triés par date_besoin ASC (ordre chronologique)
   * - Dons triés par date_saisie ASC (FIFO)
   *
   * @param bool $isSimulation Si true, ne fait qu'une simulation sans modification de la BDD
   */
  private function executeDispatchByDate(bool $isSimulation = false)
  {
    $stats = [
      'total_dispatches' => 0,
      'total_quantity_dispatched' => 0,
      'besoins_satisfaits' => 0,
      'dons_utilises' => 0,
      'details' => [],
      'mode' => 'date'
    ];

    try {
      // 1. Récupérer les besoins non satisfaits (triés par date_besoin ASC)
      $besoins = $this->donRepository->getBesoinsNonSatisfaits();

      // 2. Récupérer les dons non utilisés totalement (triés FIFO par date de saisie)
      $dons = $this->donRepository->getDonsNonUtilises();

      // 3. Grouper les dons par libellé (case-insensitive) pour un accès rapide
      $donsByLibelle = [];
      foreach ($dons as $don) {
        // Normaliser le libellé (lowercase, trim)
        $libelleKey = $don['libelle'] === null ? '__ARGENT__' : strtolower(trim($don['libelle']));

        if (!isset($donsByLibelle[$libelleKey])) {
          $donsByLibelle[$libelleKey] = [];
        }
        $donsByLibelle[$libelleKey][] = $don;
      }

      // 4. Algorithme : traiter chaque besoin selon l'ordre défini
      foreach ($besoins as $besoin) {
        $besoinId = $besoin['id'];
        $besoinLibelle = $besoin['libelle'];
        $quantityNeed = $besoin['quantity_restante'];

        // Normaliser le libellé du besoin
        $libelleKey = $besoinLibelle === null ? '__ARGENT__' : strtolower(trim($besoinLibelle));

        // Vérifier s'il existe des dons pour ce libellé exact
        if (!isset($donsByLibelle[$libelleKey]) || empty($donsByLibelle[$libelleKey])) {
          continue;
        }

        // Variable pour suivre la progression
        $quantityRemaining = $quantityNeed;

        // 5. Dispatcher les dons selon FIFO jusqu'à satisfaction du besoin
        foreach ($donsByLibelle[$libelleKey] as &$don) {
          if ($quantityRemaining <= 0) {
            break; // Le besoin est complètement satisfait
          }

          if ($don['quantity_restante'] <= 0) {
            continue; // Ce don est épuisé, passer au suivant
          }

          $donId = $don['id'];
          $quantityAvailable = $don['quantity_restante'];

          // Calculer la quantité à dispatcher
          $quantityToDispatch = min($quantityAvailable, $quantityRemaining);

          // 6. Si ce n'est pas une simulation, enregistrer dans la base de données
          if (!$isSimulation) {
            // Insertion dans la table dispatch
            $this->donRepository->insertDispatch($donId, $besoinId, $quantityToDispatch);

            // Mise à jour des quantités restantes
            $this->donRepository->updateQuantityRestanteDon($donId, $quantityToDispatch);
            $this->donRepository->updateQuantityRestanteBesoin($besoinId, $quantityToDispatch);
          }

          // Mettre à jour les variables locales
          $don['quantity_restante'] -= $quantityToDispatch;
          $quantityRemaining -= $quantityToDispatch;

          // Enregistrer les statistiques
          $stats['total_dispatches']++;
          $stats['total_quantity_dispatched'] += $quantityToDispatch;
          $stats['details'][] = [
            'don_id' => $donId,
            'besoin_id' => $besoinId,
            'type' => $don['type_libelle'],
            'libelle' => $don['libelle'] ?? 'Argent',
            'ville' => $besoin['ville_nom'],
            'besoin_libelle' => $besoin['libelle'] ?? 'Argent',
            'besoin_date' => $besoin['date_besoin'],
            'quantity' => $quantityToDispatch,
            'don_date' => $don['date_saisie']
          ];

          // Compter les dons totalement utilisés
          if ($don['quantity_restante'] == 0) {
            $stats['dons_utilises']++;
          }
        }

        // Compter les besoins totalement satisfaits
        if ($quantityRemaining == 0) {
          $stats['besoins_satisfaits']++;
        }
      }

      return $stats;

    } catch (Exception $e) {
      throw new Exception("Erreur lors du dispatch : " . $e->getMessage());
    }
  }

  /**
   * Exécute ou simule le dispatch des dons en mode quantité croissante
   * LOGIQUE:
   * - Les dons sont matchés par libellé exact avec les besoins (ex: don "Riz" → besoin "Riz")
   * - Pour l'argent, libelle = NULL pour les deux
   * - Besoins triés par quantity_restante ASC (les plus petits besoins en premier)
   * - Dons triés par quantity_restante ASC (les plus petits dons en premier)
   *
   * @param bool $isSimulation Si true, ne fait qu'une simulation sans modification de la BDD
   */
  private function executeDispatchByQuantity(bool $isSimulation = false)
  {
    $stats = [
      'total_dispatches' => 0,
      'total_quantity_dispatched' => 0,
      'besoins_satisfaits' => 0,
      'dons_utilises' => 0,
      'details' => [],
      'mode' => 'quantity'
    ];

    try {
      // 1. Récupérer les besoins non satisfaits
      $besoins = $this->donRepository->getBesoinsNonSatisfaits();

      // Trier par quantity_restante ASC (les plus petits besoins en premier)
      usort($besoins, function ($a, $b) {
        $cmp = $a['quantity_restante'] <=> $b['quantity_restante'];
        if ($cmp !== 0)
          return $cmp;
        // En cas d'égalité, trier par date puis par ID
        $cmp = strcmp($a['date_besoin'], $b['date_besoin']);
        if ($cmp !== 0)
          return $cmp;
        return $a['id'] <=> $b['id'];
      });

      // 2. Récupérer les dons non utilisés totalement
      $dons = $this->donRepository->getDonsNonUtilises();

      // 3. Grouper les dons par libellé (case-insensitive) pour un accès rapide
      $donsByLibelle = [];
      foreach ($dons as $don) {
        // Normaliser le libellé (lowercase, trim)
        $libelleKey = $don['libelle'] === null ? '__ARGENT__' : strtolower(trim($don['libelle']));

        if (!isset($donsByLibelle[$libelleKey])) {
          $donsByLibelle[$libelleKey] = [];
        }
        $donsByLibelle[$libelleKey][] = $don;
      }

      // Trier chaque groupe de dons par quantité croissante
      foreach ($donsByLibelle as $libelleKey => $donsList) {
        usort($donsList, function ($a, $b) {
          $cmp = $a['quantity_restante'] <=> $b['quantity_restante'];
          if ($cmp !== 0)
            return $cmp;
          // En cas d'égalité, trier par date puis par ID
          $cmp = strcmp($a['date_saisie'], $b['date_saisie']);
          if ($cmp !== 0)
            return $cmp;
          return $a['id'] <=> $b['id'];
        });
        $donsByLibelle[$libelleKey] = $donsList;
      }

      // 4. Algorithme : traiter chaque besoin selon l'ordre défini
      foreach ($besoins as $besoin) {
        $besoinId = $besoin['id'];
        $besoinLibelle = $besoin['libelle'];
        $quantityNeed = $besoin['quantity_restante'];

        // Normaliser le libellé du besoin
        $libelleKey = $besoinLibelle === null ? '__ARGENT__' : strtolower(trim($besoinLibelle));

        // Vérifier s'il existe des dons pour ce libellé exact
        if (!isset($donsByLibelle[$libelleKey]) || empty($donsByLibelle[$libelleKey])) {
          continue;
        }

        // Variable pour suivre la progression
        $quantityRemaining = $quantityNeed;

        // 5. Dispatcher les dons jusqu'à satisfaction du besoin
        foreach ($donsByLibelle[$libelleKey] as &$don) {
          if ($quantityRemaining <= 0) {
            break; // Le besoin est complètement satisfait
          }

          if ($don['quantity_restante'] <= 0) {
            continue; // Ce don est épuisé, passer au suivant
          }

          $donId = $don['id'];
          $quantityAvailable = $don['quantity_restante'];

          // Calculer la quantité à dispatcher
          $quantityToDispatch = min($quantityAvailable, $quantityRemaining);

          // 6. Si ce n'est pas une simulation, enregistrer dans la base de données
          if (!$isSimulation) {
            // Insertion dans la table dispatch
            $this->donRepository->insertDispatch($donId, $besoinId, $quantityToDispatch);

            // Mise à jour des quantités restantes
            $this->donRepository->updateQuantityRestanteDon($donId, $quantityToDispatch);
            $this->donRepository->updateQuantityRestanteBesoin($besoinId, $quantityToDispatch);
          }

          // Mettre à jour les variables locales
          $don['quantity_restante'] -= $quantityToDispatch;
          $quantityRemaining -= $quantityToDispatch;

          // Enregistrer les statistiques
          $stats['total_dispatches']++;
          $stats['total_quantity_dispatched'] += $quantityToDispatch;
          $stats['details'][] = [
            'don_id' => $donId,
            'besoin_id' => $besoinId,
            'type' => $don['type_libelle'],
            'libelle' => $don['libelle'] ?? 'Argent',
            'ville' => $besoin['ville_nom'],
            'besoin_libelle' => $besoin['libelle'] ?? 'Argent',
            'besoin_date' => $besoin['date_besoin'],
            'quantity' => $quantityToDispatch,
            'don_date' => $don['date_saisie']
          ];

          // Compter les dons totalement utilisés
          if ($don['quantity_restante'] == 0) {
            $stats['dons_utilises']++;
          }
        }

        // Compter les besoins totalement satisfaits
        if ($quantityRemaining == 0) {
          $stats['besoins_satisfaits']++;
        }
      }

      return $stats;

    } catch (Exception $e) {
      throw new Exception("Erreur lors du dispatch par quantité : " . $e->getMessage());
    }
  }

  /**
   * Crée un nouveau don (sans besoin spécifique)
   * Les dons spécifient ce qui est donné : Riz, Eau, Huile, etc.
   * Pour l'argent, libelle = NULL
   */
  public function createDon($idTypeBesoin, $libelle, $quantity, $dateSaisie = null)
  {
    if ($quantity <= 0) {
      throw new Exception("La quantité doit être supérieure à 0");
    }

    if ($dateSaisie === null) {
      $dateSaisie = date('Y-m-d');
    }

    return $this->donRepository->createDon($idTypeBesoin, $libelle, $quantity, $dateSaisie);
  }

  public function getAllDons()
  {
    return $this->donRepository->getAllDons();
  }

  public function getDispatchHistory()
  {
    return $this->donRepository->getDispatchHistory();
  }

  public function getReport()
  {
    $besoins = $this->donRepository->getBesoinsNonSatisfaits();
    $dons = $this->donRepository->getDonsNonUtilises();

    $report = [
      'besoins_non_satisfaits' => [
        'count' => count($besoins),
        'total_quantity' => array_sum(array_column($besoins, 'quantity_restante')),
        'details' => $besoins
      ],
      'dons_non_utilises' => [
        'count' => count($dons),
        'total_quantity' => array_sum(array_column($dons, 'quantity_restante')),
        'details' => $dons
      ]
    ];

    return $report;
  }

  /**
   * Exécute ou simule le dispatch des dons en mode proportionnel
   * LOGIQUE:
   * - Les dons sont matchés par libellé exact avec les besoins
   * - Pour chaque groupe de besoins avec le même libellé, on distribue les dons proportionnellement
   * - Calcul: (quantité_besoin / total_besoins) * quantité_don_disponible
   * - Arrondi intelligent: on garde les parties entières, puis on arrondit vers le haut
   *   les décimales les plus élevées jusqu'à atteindre le total exact du don
   *
   * Exemple: Don de 5 unités, besoins de 1, 3, 5 (total=9)
   * - Proportions: 0.555, 1.666, 2.777
   * - Parties entières: 0, 1, 2 (somme=3)
   * - Reste à distribuer: 2
   * - Décimales triées: 0.777, 0.666, 0.555
   * - On ajoute 1 aux 2 premiers: 3, 2, 0 (total=5)
   *
   * @param bool $isSimulation Si true, ne fait qu'une simulation sans modification de la BDD
   */
  private function executeDispatchByProportional(bool $isSimulation = false)
  {
    $stats = [
      'total_dispatches' => 0,
      'total_quantity_dispatched' => 0,
      'besoins_satisfaits' => 0,
      'dons_utilises' => 0,
      'details' => [],
      'mode' => 'proportional'
    ];

    try {
      // 1. Récupérer les besoins non satisfaits
      $besoins = $this->donRepository->getBesoinsNonSatisfaits();

      // 2. Récupérer les dons non utilisés totalement
      $dons = $this->donRepository->getDonsNonUtilises();

      // 3. Grouper besoins ET dons par libellé
      $besoinsByLibelle = [];
      foreach ($besoins as $besoin) {
        $libelleKey = $besoin['libelle'] === null ? '__ARGENT__' : strtolower(trim($besoin['libelle']));
        if (!isset($besoinsByLibelle[$libelleKey])) {
          $besoinsByLibelle[$libelleKey] = [];
        }
        $besoinsByLibelle[$libelleKey][] = $besoin;
      }

      $donsByLibelle = [];
      foreach ($dons as $don) {
        $libelleKey = $don['libelle'] === null ? '__ARGENT__' : strtolower(trim($don['libelle']));
        if (!isset($donsByLibelle[$libelleKey])) {
          $donsByLibelle[$libelleKey] = [];
        }
        $donsByLibelle[$libelleKey][] = $don;
      }

      // 4. Pour chaque groupe de libellé, faire la distribution proportionnelle
      foreach ($besoinsByLibelle as $libelleKey => $besoinsGroup) {
        if (empty($donsByLibelle[$libelleKey])) {
          continue;
        }

        // Calculer le total des dons disponibles pour ce libellé
        $totalDonRestant = 0;
        foreach ($donsByLibelle[$libelleKey] as $don) {
          $totalDonRestant += (int) $don['quantity_restante'];
        }
        if ($totalDonRestant <= 0) {
          continue;
        }

        // Calculer le total des besoins pour ce libellé
        $sumNeeds = 0;
        foreach ($besoinsGroup as $b) {
          $sumNeeds += (int) $b['quantity_restante'];
        }
        if ($sumNeeds <= 0) {
          continue;
        }

        // Calculer les distributions proportionnelles avec arrondi intelligent
        $distributions = [];
        $totalFloor = 0;

        foreach ($besoinsGroup as $index => $besoin) {
          $need = (int) $besoin['quantity_restante'];
          $proportion = ($need / $sumNeeds) * $totalDonRestant;
          $floor = (int) floor($proportion);
          $decimal = $proportion - $floor;

          $distributions[$index] = [
            'besoin' => $besoin,
            'proportion' => $proportion,
            'floor' => $floor,
            'decimal' => $decimal,
            'final' => $floor
          ];
          $totalFloor += $floor;
        }

        // Distribuer le reste en arrondissant vers le haut les plus grandes décimales
        $remainder = $totalDonRestant - $totalFloor;
        if ($remainder > 0) {
          // Trier par décimale décroissante
          usort($distributions, function ($a, $b) {
            return $b['decimal'] <=> $a['decimal'];
          });

          // Ajouter 1 aux premiers éléments
          for ($i = 0; $i < $remainder && $i < count($distributions); $i++) {
            $distributions[$i]['final']++;
          }
        }

        // Dispatcher les dons selon les quantités calculées
        foreach ($distributions as $distrib) {
          $besoin = $distrib['besoin'];
          $targetQuantity = $distrib['final'];

          if ($targetQuantity <= 0) {
            continue;
          }

          $besoinId = $besoin['id'];
          $remaining = $targetQuantity;

          // Consommer les dons FIFO pour ce besoin
          foreach ($donsByLibelle[$libelleKey] as &$don) {
            if ($remaining <= 0) {
              break;
            }

            $available = (int) $don['quantity_restante'];
            if ($available <= 0) {
              continue;
            }

            $donId = $don['id'];
            $qty = min($available, $remaining);

            // Enregistrer le dispatch
            if (!$isSimulation) {
              $this->donRepository->insertDispatch($donId, $besoinId, $qty);
              $this->donRepository->updateQuantityRestanteDon($donId, $qty);
              $this->donRepository->updateQuantityRestanteBesoin($besoinId, $qty);
            }

            $don['quantity_restante'] -= $qty;
            $remaining -= $qty;

            $stats['total_dispatches']++;
            $stats['total_quantity_dispatched'] += $qty;
            $stats['details'][] = [
              'don_id' => $donId,
              'besoin_id' => $besoinId,
              'type' => $don['type_libelle'],
              'libelle' => $don['libelle'] ?? 'Argent',
              'ville' => $besoin['ville_nom'],
              'besoin_libelle' => $besoin['libelle'] ?? 'Argent',
              'besoin_date' => $besoin['date_besoin'],
              'quantity' => $qty,
              'don_date' => $don['date_saisie']
            ];

            if ((int) $don['quantity_restante'] === 0) {
              $stats['dons_utilises']++;
            }
          }

          if ($remaining === 0) {
            $stats['besoins_satisfaits']++;
          }
        }
      }

      return $stats;

    } catch (Exception $e) {
      throw new Exception("Erreur lors du dispatch proportionnel : " . $e->getMessage());
    }
  }


  public function getDashboardData(): array
  {
    return [
      'villes' => $this->donRepository->getVillesWithBesoinsAndDons(),
      'totaux' => $this->donRepository->getGlobalDonTotals(),
    ];
  }

  /**
   * Récupère les statistiques de récapitulation
   */
  public function getRecapStatistics(): array
  {
    return $this->donRepository->getRecapStatistics();
  }

  /**
   * Réinitialiser tous les dispatches et quantités restantes
   * - Supprime tous les dispatches
   * - Réinitialise les quantités restantes des dons à leur valeur initiale
   * - Réinitialise les quantités restantes des besoins à leur valeur initiale
   * - Réinitialise les achats
   */
  public function reinitialiser()
  {
    try {
      $besoinRepo = new BesoinRepository(Flight::db());
      $achatRepo = new AchatRepository(Flight::db());

      $this->donRepository->reinitialiserDispatch();

      $dons = $this->donRepository->getAllDons();

      foreach ($dons as $don) {
        $donId = is_array($don) ? $don['id'] : $don->id;
        $quantity = is_array($don) ? $don['quantity'] : $don->quantity;
        $this->donRepository->reinitialiserQuantite($donId, $quantity);
      }

      // 4. Récupérer tous les besoins
      $besoins = $besoinRepo->all();

      foreach ($besoins as $besoin) {
        $besoinId = is_array($besoin) ? $besoin['id'] : $besoin->id;
        $quantity = is_array($besoin) ? $besoin['quantity'] : $besoin->quantity;
        $besoinRepo->reinitialiserQuantite($besoinId, $quantity);
      }
      $achatRepo->reinitialiser();

    } catch (Exception $e) {
      throw new Exception("Erreur lors de la réinitialisation : " . $e->getMessage());
    }
  }
}
