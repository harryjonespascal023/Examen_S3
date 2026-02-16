<?php
namespace app\repository;

use flight\database\PdoWrapper;
use flight\util\Collection;

class TypeBesoinRepository
{
	private PdoWrapper $pdo;

	public function __construct(PdoWrapper $pdo)
	{
		$this->pdo = $pdo;
	}

	public function all(): array
	{
		return $this->pdo->fetchAll(
			'SELECT id, libelle FROM BNR_type_besoin ORDER BY id DESC'
		);
	}

	public function find(int $id): ?Collection
	{
		$row = $this->pdo->fetchRow(
			'SELECT id, libelle FROM BNR_type_besoin WHERE id = ?',
			[$id]
		);

		return $row instanceof Collection && $row->count() > 0 ? $row : null;
	}

	public function create(string $libelle): int
	{
		$this->pdo->runQuery(
			'INSERT INTO BNR_type_besoin (libelle) VALUES (?)',
			[$libelle]
		);

		return (int)$this->pdo->lastInsertId();
	}

	public function update(int $id, string $libelle): void
	{
		$this->pdo->runQuery(
			'UPDATE BNR_type_besoin SET libelle = ? WHERE id = ?',
			[$libelle, $id]
		);
	}

	public function delete(int $id): void
	{
		$this->pdo->runQuery(
			'DELETE FROM BNR_type_besoin WHERE id = ?',
			[$id]
		);
	}
}

