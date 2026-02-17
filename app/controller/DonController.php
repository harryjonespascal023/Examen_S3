<?php

namespace app\controller;

use app\repository\DonRepository;
use app\services\DonService;
use Exception;
use Flight;

class DonController
{
  private $donService;

  public function __construct()
  {
    $this->donService = new DonService(new DonRepository(Flight::db()));
  }

  public function index()
  {
    $dons = $this->donService->getAllDons();
    $report = $this->donService->getReport();
    $typesBesoin = $this->donService->getAllTypesBesoin();
    $message = Flight::request()->query->message ?? null;
    $messageType = Flight::request()->query->type ?? 'info';

    Flight::render('dons/index', [
      'dons' => $dons,
      'report' => $report,
      'typesBesoin' => $typesBesoin,
      'message' => $message,
      'messageType' => $messageType
    ]);
  }

  public function createForm()
  {
    try {
      $idTypeBesoin = Flight::request()->data->id_type_besoin ?? null;
      $libelle = trim(Flight::request()->data->libelle ?? '');
      $quantity = Flight::request()->data->quantity ?? null;
      $dateSaisie = Flight::request()->data->date_saisie ?? date('Y-m-d');

      if (!$idTypeBesoin || !$quantity) {
        Flight::redirect('/dons?message=' . urlencode('Type de besoin et quantité requis') . '&type=danger');
        return;
      }

      if ($quantity <= 0) {
        Flight::redirect('/dons?message=' . urlencode('La quantité doit être supérieure à 0') . '&type=danger');
        return;
      }

      // Si le libellé est vide, mettre NULL (pour l'argent)
      $libelle = empty($libelle) ? null : $libelle;

      $donId = $this->donService->createDon($idTypeBesoin, $libelle, $quantity, $dateSaisie);
      Flight::redirect('/dons?message=' . urlencode('Don créé avec succès (ID: ' . $donId . ')') . '&type=success');

    } catch (Exception $e) {
      Flight::redirect('/dons?message=' . urlencode('Erreur: ' . $e->getMessage()) . '&type=danger');
    }
  }

  public function dispatchForm()
  {
    try {
      $mode = Flight::request()->data->mode ?? 'date';
      $stats = $this->donService->dispatchDons($mode);

      // Déterminer le label du mode
      if ($mode === 'quantity') {
        $modeLabel = 'quantité croissante';
      } elseif ($mode === 'proportional') {
        $modeLabel = 'proportionnel';
      } else {
        $modeLabel = 'date (FIFO)';
      }

      $message = sprintf(
        'Dispatch réussi (%s): %d dispatches, %d unités dispatchées, %d besoins satisfaits',
        $modeLabel,
        $stats['total_dispatches'],
        $stats['total_quantity_dispatched'],
        $stats['besoins_satisfaits']
      );
      Flight::redirect('/dons?message=' . urlencode($message) . '&type=success');
    } catch (Exception $e) {
      Flight::redirect('/dons?message=' . urlencode('Erreur dispatch: ' . $e->getMessage()) . '&type=danger');
    }
  }

  /**
   * Page de simulation
   */
  public function simulationPage()
  {
    $message = Flight::request()->query->message ?? null;
    $messageType = Flight::request()->query->type ?? 'info';

    Flight::render('dons/simulation', [
      'message' => $message,
      'messageType' => $messageType,
      'simulationResult' => null
    ]);
  }

  public function simulate()
  {
    try {
      $mode = Flight::request()->data->mode ?? 'date';
      $stats = $this->donService->simulateDispatch($mode);

      Flight::render('dons/simulation', [
        'message' => 'Simulation terminée avec succès',
        'messageType' => 'success',
        'simulationResult' => $stats
      ]);

    } catch (Exception $e) {
      Flight::render('dons/simulation', [
        'message' => 'Erreur simulation: ' . $e->getMessage(),
        'messageType' => 'danger',
        'simulationResult' => null
      ]);
    }
  }

  public function history()
  {
    try {
      $history = $this->donService->getDispatchHistory();

      Flight::render('dons/history', [
        'history' => $history
      ]);

    } catch (Exception $e) {
      Flight::json([
        'success' => false,
        'message' => 'Erreur lors de la récupération de l\'historique : ' . $e->getMessage()
      ], 500);
    }
  }

  public function dashboard(): void
  {
    $data = $this->donService->getDashboardData();

    Flight::render('dashboard', [
      'villes' => $data['villes'] ?? [],
      'totaux' => $data['totaux'] ?? [
        'total_recus' => 0,
        'total_attribues' => 0,
        'total_reste' => 0,
      ],
    ]);
  }

  /**
   * Page de récapitulation
   */
  public function recapitulation(): void
  {
    $stats = $this->donService->getRecapStatistics();

    Flight::render('dons/recapitulation', [
      'stats' => $stats
    ]);
  }

  public function recapitulationAjax(): void
  {
    try {
      $stats = $this->donService->getRecapStatistics();

      Flight::json([
        'success' => true,
        'data' => $stats
      ]);

    } catch (Exception $e) {
      Flight::json([
        'success' => false,
        'message' => 'Erreur lors de la récupération des statistiques : ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Réinitialiser tous les dispatches et quantités
   */
  public function reinitialiser(): void
  {
    try {
      $this->donService->reinitialiser();
      Flight::redirect('/dashboard?message=' . urlencode('Réinitialisation effectuée avec succès : tous les dispatches ont été supprimés et les quantités réinitialisées') . '&type=success');
    } catch (Exception $e) {
      Flight::redirect('/dashboard?message=' . urlencode('Erreur lors de la réinitialisation : ' . $e->getMessage()) . '&type=danger');
    }
  }
}
