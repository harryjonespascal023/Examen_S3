<?php

namespace app\services;

use app\repository\EchangeRepository;
use app\repository\ObjetRepository;
use Flight;

class EchangeService
{
  private EchangeRepository $echangeRepository;
  private ObjetRepository $objetRepository;

  public function __construct()
  {
    $this->echangeRepository = new EchangeRepository(Flight::db());
    $this->objetRepository = new ObjetRepository(Flight::db());
  }

  public function proposerEchange($objetDemandeId, $objetProposeId, $user1Id, $user2Id)
  {
    return $this->echangeRepository->createEchange($objetDemandeId, $objetProposeId, $user1Id, $user2Id);
  }

  public function getIncoming($userId)
  {
    return $this->echangeRepository->getIncomingByUser($userId);
  }

  public function getOutgoing($userId)
  {
    return $this->echangeRepository->getOutgoingByUser($userId);
  }

  public function accepterEchange($echangeId)
  {
    $echange = $this->echangeRepository->getEchangeById($echangeId);
    if (!$echange || (int) $echange['statut'] !== 0) {
      return false;
    }

    $this->echangeRepository->updateStatut($echangeId, 1);

    $objetDemandeId = $echange['objet_id'];
    $objetProposeId = $echange['objet_propose_id'];
    $user1Id = $echange['user1_id'];
    $user2Id = $echange['user2_id'];

    $this->objetRepository->updateObjetOwner($objetDemandeId, $user1Id);
    $this->objetRepository->updateObjetOwner($objetProposeId, $user2Id);

    $this->objetRepository->addHistoriqueObjet($objetDemandeId, $user1Id);
    $this->objetRepository->addHistoriqueObjet($objetProposeId, $user2Id);

    return true;
  }

  public function refuserEchange($echangeId)
  {
    $echange = $this->echangeRepository->getEchangeById($echangeId);
    if (!$echange || (int) $echange['statut'] !== 0) {
      return false;
    }
    $this->echangeRepository->updateStatut($echangeId, 2);
    return true;
  }
}
