<?php

namespace App\controller;

use app\repository\ObjetRepository;
use app\services\EchangeService;
use Flight;

class EchangeController
{
  private function ensureUser(): void
  {
    if (empty($_SESSION['user_id'])) {
      Flight::redirect('/login');
      exit;
    }
  }

  public function liste()
  {
    $this->ensureUser();
    $service = new EchangeService();
    $incoming = $service->getIncoming($_SESSION['user_id']);
    $outgoing = $service->getOutgoing($_SESSION['user_id']);
    Flight::render('echanges', [
      'incoming' => $incoming,
      'outgoing' => $outgoing,
    ]);
  }

  public function proposer()
  {
    $this->ensureUser();
    $request = Flight::request();
    $objetDemandeId = $request->data['objet_demande_id'] ?? null;
    $objetProposeId = $request->data['objet_propose_id'] ?? null;

    if (!$objetDemandeId || !$objetProposeId) {
      Flight::redirect('/accueil');
      return;
    }

    $objetRepo = new ObjetRepository(Flight::db());
    $objetDemande = $objetRepo->getObjetById($objetDemandeId);
    if (!$objetDemande) {
      Flight::redirect('/accueil');
      return;
    }

    $user1Id = $_SESSION['user_id'];
    $user2Id = $objetDemande['user_id'];
    if ((int) $user1Id === (int) $user2Id) {
      Flight::redirect('/objet/' . $objetDemandeId);
      return;
    }

    $objetPropose = $objetRepo->getObjetById($objetProposeId);
    if (!$objetPropose || (int) $objetPropose['user_id'] !== (int) $user1Id) {
      Flight::redirect('/objet/' . $objetDemandeId);
      return;
    }

    $service = new EchangeService();
    $service->proposerEchange($objetDemandeId, $objetProposeId, $user1Id, $user2Id);
    Flight::redirect('/echanges');
  }

  public function decision()
  {
    $this->ensureUser();
    $request = Flight::request();
    $echangeId = $request->data['echange_id'] ?? null;
    $action = $request->data['action'] ?? '';

    if (!$echangeId) {
      Flight::redirect('/echanges');
      return;
    }

    $service = new EchangeService();
    $echangeRepo = new \app\repository\EchangeRepository(Flight::db());
    $echange = $echangeRepo->getEchangeById($echangeId);
    if (!$echange || (int) $echange['user2_id'] !== (int) $_SESSION['user_id']) {
      Flight::redirect('/echanges');
      return;
    }

    if ($action === 'accept') {
      $service->accepterEchange($echangeId);
    } elseif ($action === 'reject') {
      $service->refuserEchange($echangeId);
    }

    Flight::redirect('/echanges');
  }
}
