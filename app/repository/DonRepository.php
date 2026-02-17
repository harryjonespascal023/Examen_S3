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

  /**
   * Récupère les besoins non satisfaits, triés par ancienneté (FIFO)
   * Les besoins les plus anciens sont traités en premier
   */
  public function getBesoinsNonSatisfaits()
  {
    $query = "SELECT b.*, v.nom as ville_nom, t.libelle as type_libelle
                  FROM BNR_besoin b
                  INNER JOIN BNR_ville v ON b.id_ville = v.id
                  INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
                  WHERE b.quantity_restante > 0
                  ORDER BY b.date_besoin ASC, b.id ASC";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Récupère les dons non utilisés totalement, triés par date de saisie (FIFO)
   * Avec libellé pour matching précis avec les besoins
   */
  public function getDonsNonUtilises()
  {
    $query = "SELECT d.*, t.libelle as type_libelle
                  FROM BNR_don d
                  INNER JOIN BNR_type_besoin t ON d.id_type_besoin = t.id
                  WHERE d.quantity_restante > 0
                  ORDER BY d.date_saisie ASC, d.id ASC";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getBesoinsDisponibles()
  {
    $query = "SELECT b.id, b.libelle, b.quantity_restante, b.prix_unitaire, b.date_besoin,
                     v.nom as ville_nom, t.libelle as type_libelle
                  FROM BNR_besoin b
                  INNER JOIN BNR_ville v ON b.id_ville = v.id
                  INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
                  WHERE b.quantity_restante > 0
                  ORDER BY b.date_besoin ASC, v.nom, b.libelle";

    $result = $this->db->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Récupère tous les types de besoin pour le formulaire de don
   */
  public function getAllTypesBesoinForDonForm()
  {
    $query = "SELECT * FROM BNR_type_besoin ORDER BY libelle ASC";
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

  /**
   * Crée un nouveau don (sans lien avec un besoin spécifique)
   * Le don spécifie ce qui est donné : riz, eau, huile, etc.
   * Pour l'argent, libelle = NULL
   */
  public function createDon($idTypeBesoin, $libelle, $quantity, $dateSaisie)
  {
    $query = "INSERT INTO BNR_don (id_type_besoin, libelle, quantity, quantity_restante, date_saisie)
                  VALUES (:id_type_besoin, :libelle, :quantity, :quantity_restante, :date_saisie)";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
      ':id_type_besoin' => $idTypeBesoin,
      ':libelle' => $libelle,
      ':quantity' => $quantity,
      ':quantity_restante' => $quantity,
      ':date_saisie' => $dateSaisie
    ]);

    return $this->db->lastInsertId();
  }

  /**
   * Récupère tous les dons avec leurs informations
   */
  public function getAllDons()
  {
    $query = "SELECT d.*, t.libelle as type_libelle
                  FROM BNR_don d
                  INNER JOIN BNR_type_besoin t ON d.id_type_besoin = t.id
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
                    d.libelle as don_libelle,
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
    return $result->fetchAll(PDO::FETCH_OBJ);
  }

  public function getTypesBesoin(): array
  {
    $rows = $this->db->fetchAll('SELECT id, libelle FROM BNR_type_besoin ORDER BY id');
    $types = [];
    foreach ($rows as $row) {
      $types[] = [
        'id' => (int) $row->id,
        'libelle' => (string) $row->libelle,
      ];
    }
    return $types;
  }

  public function getVillesWithBesoinStats(): array
  {
    $types = $this->getTypesBesoin();

    $statsRows = $this->db->fetchAll(
      'SELECT
                v.id AS ville_id,
                v.nom AS ville_nom,
                tb.id AS type_id,
                tb.libelle AS type_libelle,
                COALESCE(SUM(b.quantity), 0) AS besoin_qty,
                COALESCE(SUM(b.quantity * b.prix_unitaire), 0) AS besoin_valeur,
                COALESCE(SUM(dsp.quantity), 0) AS attribue_qty,
                COALESCE(SUM(dsp.quantity * b.prix_unitaire), 0) AS attribue_valeur
            FROM BNR_ville v
            LEFT JOIN BNR_besoin b ON b.id_ville = v.id
            LEFT JOIN BNR_type_besoin tb ON tb.id = b.id_type_besoin
            LEFT JOIN BNR_dispatch dsp ON dsp.id_besoin = b.id
            GROUP BY v.id, v.nom, tb.id, tb.libelle
            ORDER BY v.id, tb.id'
    );

    $villes = [];
    foreach ($statsRows as $row) {
      $villeId = (int) $row->ville_id;
      if (!isset($villes[$villeId])) {
        $villes[$villeId] = [
          'id' => $villeId,
          'nom' => (string) $row->ville_nom,
          'types' => [],
        ];

        foreach ($types as $t) {
          $villes[$villeId]['types'][$t['libelle']] = [
            'besoin_qty' => 0,
            'attribue_qty' => 0,
            'restant_qty' => 0,
            'besoin_valeur' => 0.0,
            'attribue_valeur' => 0.0,
            'restant_valeur' => 0.0,
          ];
        }
      }

      if ($row->type_libelle === null) {
        continue;
      }

      $typeLibelle = (string) $row->type_libelle;
      if (!isset($villes[$villeId]['types'][$typeLibelle])) {
        $villes[$villeId]['types'][$typeLibelle] = [
          'besoin_qty' => 0,
          'attribue_qty' => 0,
          'restant_qty' => 0,
          'besoin_valeur' => 0.0,
          'attribue_valeur' => 0.0,
          'restant_valeur' => 0.0,
        ];
      }

      $besoinQty = (int) $row->besoin_qty;
      $attribueQty = (int) $row->attribue_qty;
      $besoinValeur = (float) $row->besoin_valeur;
      $attribueValeur = (float) $row->attribue_valeur;

      $villes[$villeId]['types'][$typeLibelle]['besoin_qty'] = $besoinQty;
      $villes[$villeId]['types'][$typeLibelle]['attribue_qty'] = $attribueQty;
      $villes[$villeId]['types'][$typeLibelle]['restant_qty'] = max(0, $besoinQty - $attribueQty);
      $villes[$villeId]['types'][$typeLibelle]['besoin_valeur'] = $besoinValeur;
      $villes[$villeId]['types'][$typeLibelle]['attribue_valeur'] = $attribueValeur;
      $villes[$villeId]['types'][$typeLibelle]['restant_valeur'] = max(0, $besoinValeur - $attribueValeur);
    }

    return array_values($villes);
  }

  public function getGlobalDonTotals(): array
  {
    $totalRecus = (int) $this->db->fetchField('SELECT COALESCE(SUM(quantity), 0) FROM BNR_don');
    $totalAttribues = (int) $this->db->fetchField('SELECT COALESCE(SUM(quantity), 0) FROM BNR_dispatch');

    return [
      'total_recus' => $totalRecus,
      'total_attribues' => $totalAttribues,
      'total_reste' => max(0, $totalRecus - $totalAttribues),
    ];
  }

  /**
   * Récupère les villes avec leurs besoins et dons détaillés
   */
  public function getVillesWithBesoinsAndDons(): array
  {
    // Récupérer toutes les villes
    $queryVilles = "SELECT * FROM BNR_ville ORDER BY nom ASC";
    $resultVilles = $this->db->query($queryVilles);
    $villes = $resultVilles->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($villes as $ville) {
      $idVille = $ville['id'];

      // Récupérer les besoins de cette ville
      $queryBesoins = "SELECT
        b.id, b.libelle, b.quantity, b.quantity_restante, b.prix_unitaire,
        t.libelle as type_libelle
        FROM BNR_besoin b
        INNER JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
        WHERE b.id_ville = ?
        ORDER BY t.libelle, b.libelle";
      $stmtBesoins = $this->db->prepare($queryBesoins);
      $stmtBesoins->execute([$idVille]);
      $besoins = $stmtBesoins->fetchAll(PDO::FETCH_ASSOC);

      // Récupérer les dons dispatchés (distribués) à cette ville via la table dispatch
      $queryDons = "SELECT
        d.id as don_id,
        d.quantity,
        d.quantity_restante,
        d.date_saisie,
        d.libelle as don_libelle,
        b.libelle as besoin_libelle,
        t.libelle as type_libelle,
        disp.quantity as quantity_dispatchee,
        disp.date_dispatch
        FROM BNR_dispatch disp
        INNER JOIN BNR_don d ON disp.id_don = d.id
        INNER JOIN BNR_besoin b ON disp.id_besoin = b.id
        INNER JOIN BNR_type_besoin t ON d.id_type_besoin = t.id
        WHERE b.id_ville = ?
        ORDER BY disp.date_dispatch DESC";
      $stmtDons = $this->db->prepare($queryDons);
      $stmtDons->execute([$idVille]);
      $dons = $stmtDons->fetchAll(PDO::FETCH_ASSOC);

      $data[] = [
        'id' => $ville['id'],
        'nom' => $ville['nom'],
        'nombre_sinistres' => $ville['nombre_sinistres'],
        'besoins' => $besoins,
        'dons' => $dons
      ];
    }

    return $data;
  }

  public function getRecapStatistics(): array
  {
    $totalBesoinsResult = $this->db->query(
      "SELECT COALESCE(SUM(
        CASE
          WHEN prix_unitaire IS NULL THEN quantity
          ELSE quantity * prix_unitaire
        END
      ), 0) as total
       FROM BNR_besoin"
    );
    $totalBesoins = (float) $totalBesoinsResult->fetch(PDO::FETCH_ASSOC)['total'];

    $totalRestantsResult = $this->db->query(
      "SELECT COALESCE(SUM(
        CASE
          WHEN prix_unitaire IS NULL THEN quantity_restante
          ELSE quantity_restante * prix_unitaire
        END
      ), 0) as total
       FROM BNR_besoin"
    );
    $totalRestants = (float) $totalRestantsResult->fetch(PDO::FETCH_ASSOC)['total'];

    // Les besoins satisfaits = besoins totaux - besoins restants
    // Cela inclut automatiquement les dons ET les achats
    $totalSatisfaits = $totalBesoins - $totalRestants;

    return [
      'total_besoins_montant' => $totalBesoins,
      'total_satisfaits_montant' => $totalSatisfaits,
      'total_restants_montant' => $totalRestants,
      'pourcentage_satisfait' => $totalBesoins > 0 ? ($totalSatisfaits / $totalBesoins * 100) : 0
    ];
  }
}

