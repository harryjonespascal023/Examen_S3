<?php
namespace app\repository;

use flight\database\PdoWrapper;
use flight\util\Collection;

class BesoinRepository
{
  private PdoWrapper $pdo;

  public function __construct(PdoWrapper $pdo)
  {
    $this->pdo = $pdo;
  }

  public function all(): array
  {
    return $this->pdo->fetchAll(
      'SELECT b.id, b.id_ville, b.id_type_besoin, v.nom AS ville_nom, t.libelle AS type_libelle, ' .
      'b.prix_unitaire, b.quantity, b.quantity_restante, b.libelle, b.date_besoin ' .
      'FROM BNR_besoin b ' .
      'INNER JOIN BNR_ville v ON v.id = b.id_ville ' .
      'INNER JOIN BNR_type_besoin t ON t.id = b.id_type_besoin ' .
      'ORDER BY b.id DESC'
    );
  }

  public function find(int $id): ?Collection
  {
    $row = $this->pdo->fetchRow(
      'SELECT id, id_ville, id_type_besoin, prix_unitaire, quantity, quantity_restante, libelle, date_besoin ' .
      'FROM BNR_besoin WHERE id = ?',
      [$id]
    );

    return $row instanceof Collection && $row->count() > 0 ? $row : null;
  }

  public function create(int $idVille, int $idType, ?float $prixUnitaire, int $quantity, int $quantityRestante, ?string $libelle, string $dateBesoin): int
  {
    $this->pdo->runQuery(
      'INSERT INTO BNR_besoin (id_ville, id_type_besoin, prix_unitaire, quantity, quantity_restante, libelle, date_besoin) VALUES (?, ?, ?, ?, ?, ?, ?)',
      [$idVille, $idType, $prixUnitaire, $quantity, $quantityRestante, $libelle, $dateBesoin]
    );

    return (int) $this->pdo->lastInsertId();
  }

  public function update(int $id, int $idVille, int $idType, ?float $prixUnitaire, int $quantity, int $quantityRestante, ?string $libelle, string $dateBesoin): void
  {
    $this->pdo->runQuery(
      'UPDATE BNR_besoin SET id_ville = ?, id_type_besoin = ?, prix_unitaire = ?, quantity = ?, quantity_restante = ?, libelle = ?, date_besoin = ? WHERE id = ?',
      [$idVille, $idType, $prixUnitaire, $quantity, $quantityRestante, $libelle, $dateBesoin, $id]
    );
  }

  public function delete(int $id): void
  {
    $this->pdo->runQuery(
      'DELETE FROM BNR_besoin WHERE id = ?',
      [$id]
    );
  }

  public function reinitialiserQuantite($id, $quantite): void
  {
      $sql = $this->pdo->prepare("UPDATE BNR_besoin SET quantity_restante = ? WHERE id = ?");
      $sql->execute([$quantite, $id]);
  }
}

