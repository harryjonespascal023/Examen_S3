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
    $besoins = $this->donService->getBesoinsDisponibles();
    $message = Flight::request()->query->message ?? null;
    $messageType = Flight::request()->query->type ?? 'info';

    Flight::render('dons/index', [
      'dons' => $dons,
      'report' => $report,
      'typesBesoin' => $typesBesoin,
      'besoins' => $besoins,
      'message' => $message,
      'messageType' => $messageType
    ]);
  }

  public function createForm()
  {
    try {
      $idBesoin = Flight::request()->data->id_besoin ?? null;
      $quantity = Flight::request()->data->quantity ?? null;
      $dateSaisie = Flight::request()->data->date_saisie ?? date('Y-m-d');

      if (!$idBesoin || !$quantity) {
        Flight::redirect('/dons?message=' . urlencode('Besoin et quantité requis') . '&type=danger');
        return;
      }

      if ($quantity <= 0) {
        Flight::redirect('/dons?message=' . urlencode('La quantité doit être supérieure à 0') . '&type=danger');
        return;
      }

      $donId = $this->donService->createDon($idBesoin, $quantity, $dateSaisie);
      Flight::redirect('/dons?message=' . urlencode('Don créé avec succès (ID: ' . $donId . ')') . '&type=success');

    } catch (Exception $e) {
      Flight::redirect('/dons?message=' . urlencode('Erreur: ' . $e->getMessage()) . '&type=danger');
    }
  }

  public function dispatchForm()
  {
    try {
      $stats = $this->donService->dispatchDons();
      $message = sprintf(
        'Dispatch réussi: %d dispatches, %d unités dispatchées, %d besoins satisfaits',
        $stats['total_dispatches'],
        $stats['total_quantity_dispatched'],
        $stats['besoins_satisfaits']
      );
      Flight::redirect('/dons?message=' . urlencode($message) . '&type=success');
    } catch (Exception $e) {
      Flight::redirect('/dons?message=' . urlencode('Erreur dispatch: ' . $e->getMessage()) . '&type=danger');
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
}
