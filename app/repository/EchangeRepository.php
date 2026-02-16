<?php

namespace App\repository;

class EchangeRepository
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function createEchange($objetDemandeId, $objetProposeId, $user1Id, $user2Id)
	{
		$sql = $this->db->prepare("INSERT INTO echanges (objet_id, objet_propose_id, user1_id, user2_id, statut, date_demande) VALUES (?, ?, ?, ?, 0, NOW())");
		$sql->execute([$objetDemandeId, $objetProposeId, $user1Id, $user2Id]);
		return $this->db->lastInsertId();
	}

	public function getIncomingByUser($userId)
	{
		$sql = $this->db->prepare("SELECT e.*, o.libelle as objet_demande, op.libelle as objet_propose,
				u1.nom as demandeur, u2.nom as proprietaire
			FROM echanges e
			JOIN objets o ON o.id = e.objet_id
			JOIN objets op ON op.id = e.objet_propose_id
			JOIN users u1 ON u1.id = e.user1_id
			JOIN users u2 ON u2.id = e.user2_id
			WHERE e.user2_id = ?
			ORDER BY e.date_demande DESC");
		$sql->execute([$userId]);
		return $sql->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getOutgoingByUser($userId)
	{
		$sql = $this->db->prepare("SELECT e.*, o.libelle as objet_demande, op.libelle as objet_propose,
				u1.nom as demandeur, u2.nom as proprietaire
			FROM echanges e
			JOIN objets o ON o.id = e.objet_id
			JOIN objets op ON op.id = e.objet_propose_id
			JOIN users u1 ON u1.id = e.user1_id
			JOIN users u2 ON u2.id = e.user2_id
			WHERE e.user1_id = ?
			ORDER BY e.date_demande DESC");
		$sql->execute([$userId]);
		return $sql->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getEchangeById($id)
	{
		$sql = $this->db->prepare("SELECT * FROM echanges WHERE id = ? LIMIT 1");
		$sql->execute([$id]);
		return $sql->fetch(\PDO::FETCH_ASSOC);
	}

	public function updateStatut($id, $statut)
	{
		$sql = $this->db->prepare("UPDATE echanges SET statut = ?, date_accept = IF(? = 1, NOW(), date_accept) WHERE id = ?");
		$sql->execute([$statut, $statut, $id]);
	}

	public function countEchangesAcceptes()
	{
		$sql = $this->db->prepare("SELECT COUNT(*) as total FROM echanges WHERE statut = 1");
		$sql->execute();
		return (int)$sql->fetch(\PDO::FETCH_ASSOC)['total'];
	}

}