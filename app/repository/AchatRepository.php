<?php
namespace app\repository;

use flight\database\PdoWrapper;
use PDO;

class AchatRepository
{
  private PdoWrapper $pdo;

  public function __construct(PdoWrapper $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * Récupère tous les achats avec les détails
   */
  public function all(): array
  {
    return $this->pdo->fetchAll(
      'SELECT
				a.id,
				a.id_ville,
				a.id_type_besoin,
				a.libelle,
				a.quantity,
				a.prix_unitaire,
				a.montant_total,
				a.frais_achat,
				a.montant_avec_frais,
				a.date_achat,
				v.nom as ville_nom,
				tb.libelle as type_libelle
			FROM BNR_achat a
			INNER JOIN BNR_ville v ON a.id_ville = v.id
			INNER JOIN BNR_type_besoin tb ON a.id_type_besoin = tb.id
			ORDER BY a.date_achat DESC'
    );
  }

  /**
   * Récupère les achats filtrés par ville
   */
  public function getByVille(int $idVille): array
  {
    return $this->pdo->fetchAll(
      'SELECT
				a.id,
				a.id_ville,
				a.id_type_besoin,
				a.libelle,
				a.quantity,
				a.prix_unitaire,
				a.montant_total,
				a.frais_achat,
				a.montant_avec_frais,
				a.date_achat,
				v.nom as ville_nom,
				tb.libelle as type_libelle
			FROM BNR_achat a
			INNER JOIN BNR_ville v ON a.id_ville = v.id
			INNER JOIN BNR_type_besoin tb ON a.id_type_besoin = tb.id
			WHERE a.id_ville = ?
			ORDER BY a.date_achat DESC',
      [$idVille]
    );
  }

  /**
   * Crée un achat
   */
  public function create(
    int $idVille,
    int $idTypeBesoin,
    string $libelle,
    int $quantity,
    float $prixUnitaire,
    float $montantTotal,
    float $fraisAchat,
    float $montantAvecFrais,
    string $dateAchat,
    int $idBesoin
  ): int {
    // Commencer une transaction
    $this->pdo->beginTransaction();

    try {
      // 1. Créer l'achat (enregistrement historique)
      $this->pdo->runQuery(
        'INSERT INTO BNR_achat
				(id_ville, id_type_besoin, libelle, quantity, prix_unitaire, montant_total, frais_achat, montant_avec_frais, date_achat)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
          $idVille,
          $idTypeBesoin,
          $libelle,
          $quantity,
          $prixUnitaire,
          $montantTotal,
          $fraisAchat,
          $montantAvecFrais,
          $dateAchat
        ]
      );
      $achatId = (int) $this->pdo->lastInsertId();

      // 2. Déduire l'argent des dons en argent (FIFO)
      $this->deduireArgentDesDons($montantAvecFrais);

      // 3. Diminuer la quantity_restante du besoin acheté (on satisfait le besoin)
      $this->pdo->runQuery(
        'UPDATE BNR_besoin SET quantity_restante = quantity_restante - ? WHERE id = ?',
        [$quantity, $idBesoin]
      );

      $this->pdo->commit();
      return $achatId;

    } catch (\Exception $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }

  /**
   * Déduit l'argent des dons en argent selon FIFO
   */
  private function deduireArgentDesDons(float $montant): void
  {
    // Récupérer les dons en argent avec quantity_restante > 0 (FIFO par date)
    $donsArgent = $this->pdo->fetchAll(
      'SELECT d.id, d.quantity_restante
			FROM BNR_don d
			INNER JOIN BNR_type_besoin tb ON d.id_type_besoin = tb.id
			WHERE tb.libelle = \'argent\' AND d.quantity_restante > 0
			ORDER BY d.date_saisie ASC, d.id ASC'
    );

    $montantRestant = $montant;

    foreach ($donsArgent as $don) {
      if ($montantRestant <= 0) {
        break;
      }

      $disponible = (float) $don['quantity_restante'];
      $aDeduite = min($disponible, $montantRestant);

      // Mettre à jour le don
      $this->pdo->runQuery(
        'UPDATE BNR_don SET quantity_restante = quantity_restante - ? WHERE id = ?',
        [$aDeduite, (int) $don['id']]
      );

      $montantRestant -= $aDeduite;
    }

    if ($montantRestant > 0) {
      throw new \Exception("Fonds insuffisants pour déduire le montant total");
    }
  }

  /**
   * Récupère le pourcentage de frais d'achat depuis la configuration
   */
  public function getFraisAchatPourcentage(): float
  {
    $result = $this->pdo->fetchRow(
      'SELECT valeur FROM BNR_config WHERE cle = ?',
      ['frais_achat_pourcentage']
    );

    return $result ? (float) $result['valeur'] : 10.0;
  }

  /**
   * Met à jour le pourcentage de frais d'achat
   */
  public function updateFraisAchatPourcentage(float $pourcentage): void
  {
    $this->pdo->runQuery(
      'UPDATE BNR_config SET valeur = ? WHERE cle = ?',
      [$pourcentage, 'frais_achat_pourcentage']
    );
  }

  /**
   * Récupère les besoins restants (non satisfaits) par ville
   */
  public function getBesoinsRestants(?int $idVille = null): array
  {
    $sql = 'SELECT
			b.id,
			b.id_ville,
			b.id_type_besoin,
			b.libelle,
			b.quantity,
			b.quantity_restante,
			b.prix_unitaire,
			v.nom as ville_nom,
			tb.libelle as type_libelle,
			(b.quantity_restante * b.prix_unitaire) as montant_restant
		FROM BNR_besoin b
		INNER JOIN BNR_ville v ON b.id_ville = v.id
		INNER JOIN BNR_type_besoin tb ON b.id_type_besoin = tb.id
		WHERE b.quantity_restante > 0
		AND tb.libelle IN (\'nature\', \'materiaux\')';

    $params = [];
    if ($idVille !== null) {
      $sql .= ' AND b.id_ville = ?';
      $params[] = $idVille;
    }

    $sql .= ' ORDER BY v.nom, tb.libelle, b.libelle';

    return $this->pdo->fetchAll($sql, $params);
  }

  /**
   * Récupère le total des dons en argent restants
   */
  public function getTotalDonsArgentRestants(): float
  {
    $result = $this->pdo->fetchRow(
      'SELECT COALESCE(SUM(d.quantity_restante), 0) as total
			FROM BNR_don d
			INNER JOIN BNR_type_besoin tb ON d.id_type_besoin = tb.id
			WHERE tb.libelle = \'argent\' AND d.quantity_restante > 0'
    );

    return $result ? (float) $result['total'] : 0.0;
  }

  /**
   * Vérifie si un achat existe déjà dans les dons restants
   * Note: Avec la nouvelle architecture, les dons ne sont plus liés à une ville spécifique
   * On vérifie juste l'existence de dons avec le type et libellé correspondants
   */
  public function achatExisteDansDoonsRestants(int $idVille, int $idTypeBesoin, string $libelle): bool
  {
    $result = $this->pdo->fetchRow(
      'SELECT COUNT(*) as count
			FROM BNR_don d
			WHERE d.id_type_besoin = ?
			AND (d.libelle = ? OR (d.libelle IS NULL AND ? IS NULL))
			AND d.quantity_restante > 0',
      [$idTypeBesoin, $libelle, $libelle]
    );

    return $result && (int) $result['count'] > 0;
  }
}
