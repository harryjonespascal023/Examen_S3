<?php

namespace app\services;

use app\repository\DonRepository;

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

  public function dispatchDons()
  {
    return $this->executeDispatch(false); // Exécution réelle
  }

  /**
   * Simule le dispatch des dons sans enregistrer dans la base de données
   */
  public function simulateDispatch()
  {
    return $this->executeDispatch(true); // Simulation
  }

  /**
   * Exécute ou simule le dispatch des dons
   * LOGIQUE:
   * - Les besoins les plus anciens (date_besoin) reçoivent en premier (FIFO)
   * - Les dons sont matchés par libellé exact avec les besoins (ex: don "Riz" → besoin "Riz")
   * - Pour l'argent, libelle = NULL pour les deux
   * @param bool $isSimulation Si true, ne fait qu'une simulation sans modification de la BDD
   */
  private function executeDispatch(bool $isSimulation = false)
  {
    $stats = [
      'total_dispatches' => 0,
      'total_quantity_dispatched' => 0,
      'besoins_satisfaits' => 0,
      'dons_utilises' => 0,
      'details' => [],
    ];

    try {
      // 1. Récupérer les besoins non satisfaits (triés par date_besoin ASC - les plus anciens en premier)
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

      // 4. Algorithme FIFO : traiter chaque besoin (du plus ancien au plus récent)
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

  private function executeDispatcCroissant(){
    $stats = [
      'total_dispatches' => 0,
      'total_quantity_dispatched' => 0,
      'besoins_satisfaits' => 0,
      'dons_utilises' => 0,
      'details' => [],
    ];

    try {
      $besoins = $this->donRepository->getBesoinsNonSatisfaits();
      $dons = $this->donRepository->getDonsNonUtilises();

      usort($besoins, function ($a, $b) {
        $cmp = $a['quantity_restante'] <=> $b['quantity_restante'];
        if ($cmp !== 0) {
          return $cmp;
        }
        $cmp = strcmp($a['date_besoin'], $b['date_besoin']);
        if ($cmp !== 0) {
          return $cmp;
        }
        return $a['id'] <=> $b['id'];
      });

      $donsByLibelle = [];
      foreach ($dons as $don) {
        $libelleKey = $don['libelle'] === null ? '__ARGENT__' : strtolower(trim($don['libelle']));

        if (!isset($donsByLibelle[$libelleKey])) {
          $donsByLibelle[$libelleKey] = [];
        }
        $donsByLibelle[$libelleKey][] = $don;
      }

      foreach ($donsByLibelle as $libelleKey => $donsList) {
        usort($donsList, function ($a, $b) {
          $cmp = $a['quantity_restante'] <=> $b['quantity_restante'];
          if ($cmp !== 0) {
            return $cmp;
          }
          $cmp = strcmp($a['date_saisie'], $b['date_saisie']);
          if ($cmp !== 0) {
            return $cmp;
          }
          return $a['id'] <=> $b['id'];
        });
        $donsByLibelle[$libelleKey] = $donsList;
      }

      foreach ($besoins as $besoin) {
        $besoinId = $besoin['id'];
        $besoinLibelle = $besoin['libelle'];
        $quantityNeed = $besoin['quantity_restante'];

        $libelleKey = $besoinLibelle === null ? '__ARGENT__' : strtolower(trim($besoinLibelle));

        if (!isset($donsByLibelle[$libelleKey]) || empty($donsByLibelle[$libelleKey])) {
          continue;
        }

        $quantityRemaining = $quantityNeed;

        foreach ($donsByLibelle[$libelleKey] as &$don) {
          if ($quantityRemaining <= 0) {
            break;
          }

          if ($don['quantity_restante'] <= 0) {
            continue;
          }

          $donId = $don['id'];
          $quantityAvailable = $don['quantity_restante'];
          $quantityToDispatch = min($quantityAvailable, $quantityRemaining);

          $this->donRepository->insertDispatch($donId, $besoinId, $quantityToDispatch);
          $this->donRepository->updateQuantityRestanteDon($donId, $quantityToDispatch);
          $this->donRepository->updateQuantityRestanteBesoin($besoinId, $quantityToDispatch);

          $don['quantity_restante'] -= $quantityToDispatch;
          $quantityRemaining -= $quantityToDispatch;

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

          if ($don['quantity_restante'] == 0) {
            $stats['dons_utilises']++;
          }
        }

        if ($quantityRemaining == 0) {
          $stats['besoins_satisfaits']++;
        }
      }

      return $stats;
    } catch (Exception $e) {
      throw new Exception("Erreur lors du dispatch croissant : " . $e->getMessage());
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
}
