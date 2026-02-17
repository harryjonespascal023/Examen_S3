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

  public function dispatchDons(string $mode = 'fifo')
  {
    return $this->executeDispatch(false, $mode);
  }

  public function simulateDispatch(string $mode = 'fifo')
  {
    return $this->executeDispatch(true, $mode);
  }

  private function executeDispatch(bool $isSimulation = false, string $mode = 'fifo')
  {
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

      if ($mode === 'proportionnel') {
        return $this->executeDispatchProportionnel($besoins, $dons, $isSimulation, $stats);
      }

      return $this->executeDispatchFifo($besoins, $dons, $isSimulation, $stats);

    } catch (\Exception $e) {
      throw new \Exception("Erreur lors du dispatch : " . $e->getMessage());
    }
  }

  private function normalizeLibelleKey($libelle): string
  {
    return $libelle === null ? '__ARGENT__' : strtolower(trim((string) $libelle));
  }

  private function executeDispatchFifo(array $besoins, array $dons, bool $isSimulation, array $stats): array
  {
    $donsByLibelle = [];
    foreach ($dons as $don) {
      $libelleKey = $this->normalizeLibelleKey($don['libelle']);
      if (!isset($donsByLibelle[$libelleKey])) {
        $donsByLibelle[$libelleKey] = [];
      }
      $donsByLibelle[$libelleKey][] = $don;
    }

    foreach ($besoins as $besoin) {
      $besoinId = $besoin['id'];
      $besoinLibelle = $besoin['libelle'];
      $quantityNeed = (int) $besoin['quantity_restante'];

      $libelleKey = $this->normalizeLibelleKey($besoinLibelle);

      if (!isset($donsByLibelle[$libelleKey]) || empty($donsByLibelle[$libelleKey])) {
        continue;
      }

      $quantityRemaining = $quantityNeed;

      foreach ($donsByLibelle[$libelleKey] as &$don) {
        if ($quantityRemaining <= 0) {
          break;
        }

        if ((int) $don['quantity_restante'] <= 0) {
          continue;
        }

        $donId = $don['id'];
        $quantityAvailable = (int) $don['quantity_restante'];

        $quantityToDispatch = min($quantityAvailable, $quantityRemaining);

        if (!$isSimulation) {
          $this->donRepository->insertDispatch($donId, $besoinId, $quantityToDispatch);
          $this->donRepository->updateQuantityRestanteDon($donId, $quantityToDispatch);
          $this->donRepository->updateQuantityRestanteBesoin($besoinId, $quantityToDispatch);
        }

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

        if ((int) $don['quantity_restante'] === 0) {
          $stats['dons_utilises']++;
        }
      }

      if ($quantityRemaining === 0) {
        $stats['besoins_satisfaits']++;
      }
    }

    return $stats;
  }

  private function executeDispatchProportionnel(array $besoins, array $dons, bool $isSimulation, array $stats): array
  {
    $besoinsByLibelle = [];
    foreach ($besoins as $besoin) {
      $libelleKey = $this->normalizeLibelleKey($besoin['libelle']);
      if (!isset($besoinsByLibelle[$libelleKey])) {
        $besoinsByLibelle[$libelleKey] = [];
      }
      $besoinsByLibelle[$libelleKey][] = $besoin;
    }

    $donsByLibelle = [];
    foreach ($dons as $don) {
      $libelleKey = $this->normalizeLibelleKey($don['libelle']);
      if (!isset($donsByLibelle[$libelleKey])) {
        $donsByLibelle[$libelleKey] = [];
      }
      $donsByLibelle[$libelleKey][] = $don;
    }

    foreach ($besoinsByLibelle as $libelleKey => $besoinsGroup) {
      if (!isset($donsByLibelle[$libelleKey]) || empty($donsByLibelle[$libelleKey])) {
        continue;
      }

      $totalDonRestant = 0;
      foreach ($donsByLibelle[$libelleKey] as $don) {
        $totalDonRestant += (int) $don['quantity_restante'];
      }
      if ($totalDonRestant <= 0) {
        continue;
      }

      $sumNeeds = 0;
      foreach ($besoinsGroup as $b) {
        $sumNeeds += (int) $b['quantity_restante'];
      }
      if ($sumNeeds <= 0) {
        continue;
      }

      $targets = [];
      foreach ($besoinsGroup as $b) {
        $need = (int) $b['quantity_restante'];
        $raw = ($totalDonRestant * $need) / $sumNeeds;
        $target = (int) floor($raw);
        if ($target > $need) {
          $target = $need;
        }
        $targets[(int) $b['id']] = $target;
      }

      $groupDons = &$donsByLibelle[$libelleKey];

      foreach ($besoinsGroup as $besoin) {
        $besoinId = (int) $besoin['id'];
        $targetForBesoin = (int) ($targets[$besoinId] ?? 0);
        if ($targetForBesoin <= 0) {
          continue;
        }

        $quantityRemainingForThisBesoin = $targetForBesoin;

        foreach ($groupDons as &$don) {
          if ($quantityRemainingForThisBesoin <= 0) {
            break;
          }

          if ((int) $don['quantity_restante'] <= 0) {
            continue;
          }

          $donId = (int) $don['id'];
          $quantityAvailable = (int) $don['quantity_restante'];

          $quantityToDispatch = min($quantityAvailable, $quantityRemainingForThisBesoin);

          if ($quantityToDispatch <= 0) {
            continue;
          }

          if (!$isSimulation) {
            $this->donRepository->insertDispatch($donId, $besoinId, $quantityToDispatch);
            $this->donRepository->updateQuantityRestanteDon($donId, $quantityToDispatch);
            $this->donRepository->updateQuantityRestanteBesoin($besoinId, $quantityToDispatch);
          }

          $don['quantity_restante'] -= $quantityToDispatch;
          $quantityRemainingForThisBesoin -= $quantityToDispatch;

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

          if ((int) $don['quantity_restante'] === 0) {
            $stats['dons_utilises']++;
          }
        }

        if ($quantityRemainingForThisBesoin === 0) {
          $stats['besoins_satisfaits']++;
        }
      }
    }
    

    return $stats;
  }

  public function createDon($idTypeBesoin, $libelle, $quantity, $dateSaisie = null)
  {
    if ($quantity <= 0) {
      throw new \Exception("La quantité doit être supérieure à 0");
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

  public function getRecapStatistics(): array
  {
    return $this->donRepository->getRecapStatistics();
  }
}