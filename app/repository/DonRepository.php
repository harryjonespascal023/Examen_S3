<?php

namespace app\repository;

use PDO;

class DonRepository
{
  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getBesoinsNonSatisfaits()
  {
    $query = "SELECT b.*, v.nom as ville_nom, t.libelle as type_libelle
                  FROM BNR_besoin b
                  INNER JOIN BNR_ville v ON b.id_ville = v.id
                  INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
                  WHERE b.quantity_restante > 0
                  ORDER BY b.id ASC";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getBesoinsDisponibles()
  {
    $query = "SELECT b.id, b.libelle, b.quantity_restante, v.nom as ville_nom, t.libelle as type_libelle
                  FROM BNR_besoin b
                  INNER JOIN BNR_ville v ON b.id_ville = v.id
                  INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
                  WHERE b.quantity_restante > 0
                  ORDER BY v.nom, b.libelle";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getDonsNonUtilises()
  {
    $query = "SELECT d.*, b.libelle as besoin_libelle, b.id_type_besoin,
                         v.nom as ville_nom, t.libelle as type_libelle
                  FROM BNR_don d
                  INNER JOIN BNR_besoin b ON d.id_besoin = b.id
                  INNER JOIN BNR_ville v ON b.id_ville = v.id
                  INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
                  WHERE d.quantity_restante > 0
                  ORDER BY d.date_saisie ASC, d.id ASC";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insertDispatch($idDon, $idBesoin, $quantity)
  {
    $query = "INSERT INTO BNR_dispatch (id_don, id_besoin, quantity, date_dispatch)
                  VALUES (:id_don, :id_besoin, :quantity, CURDATE())";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
      ':id_don' => $idDon,
      ':id_besoin' => $idBesoin,
      ':quantity' => $quantity
    ]);

    return $this->db->lastInsertId();
  }

  public function updateQuantityRestanteDon($idDon, $quantityUsed)
  {
    $query = "UPDATE BNR_don
                  SET quantity_restante = quantity_restante - :quantity_used
                  WHERE id = :id_don";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
      ':quantity_used' => $quantityUsed,
      ':id_don' => $idDon
    ]);
  }

  public function updateQuantityRestanteBesoin($idBesoin, $quantitySatisfied)
  {
    $query = "UPDATE BNR_besoin
                  SET quantity_restante = quantity_restante - :quantity_satisfied
                  WHERE id = :id_besoin";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
      ':quantity_satisfied' => $quantitySatisfied,
      ':id_besoin' => $idBesoin
    ]);
  }

  public function createDon($idBesoin, $quantity, $dateSaisie)
  {
    $query = "INSERT INTO BNR_don (id_besoin, quantity, quantity_restante, date_saisie)
                  VALUES (:id_besoin, :quantity, :quantity_restante, :date_saisie)";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
      ':id_besoin' => $idBesoin,
      ':quantity' => $quantity,
      ':quantity_restante' => $quantity,
      ':date_saisie' => $dateSaisie
    ]);

    return $this->db->lastInsertId();
  }

  public function getAllDons()
  {
    $query = "SELECT d.*, b.libelle as besoin_libelle, v.nom as ville_nom, t.libelle as type_libelle
                  FROM BNR_don d
                  INNER JOIN BNR_besoin b ON d.id_besoin = b.id
                  INNER JOIN BNR_ville v ON b.id_ville = v.id
                  INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
                  ORDER BY d.date_saisie DESC";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getDispatchHistory()
  {
    $query = "SELECT
                    dp.*,
                    d.date_saisie as don_date,
                    d.quantity as don_quantity,
                    b.libelle as besoin_libelle,
                    t.libelle as type_libelle,
                    b.quantity as besoin_quantity,
                    v.nom as ville_nom
                  FROM BNR_dispatch dp
                  INNER JOIN BNR_don d ON dp.id_don = d.id
                  INNER JOIN BNR_besoin b ON dp.id_besoin = b.id
                  INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
                  INNER JOIN BNR_ville v ON b.id_ville = v.id
                  ORDER BY dp.date_dispatch DESC, dp.id DESC";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllTypesBesoin()
  {
    $query = "SELECT * FROM BNR_type_besoin ORDER BY libelle ASC";
    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }
}
