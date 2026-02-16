<?php
namespace app\services;

use app\repository\BesoinRepository;
use flight\util\Collection;

class BesoinService
{
	private BesoinRepository $repository;

	public function __construct(BesoinRepository $repository)
	{
		$this->repository = $repository;
	}

	public function getAll(): array
	{
		return $this->repository->all();
	}

	public function getById(int $id): ?Collection
	{
		return $this->repository->find($id);
	}

	public function create(int $idVille, int $idType, float $prixUnitaire, int $quantity, int $quantityRestante, string $libelle): int
	{
		return $this->repository->create($idVille, $idType, $prixUnitaire, $quantity, $quantityRestante, $libelle);
	}

	public function update(int $id, int $idVille, int $idType, float $prixUnitaire, int $quantity, int $quantityRestante, string $libelle): void
	{
		$this->repository->update($id, $idVille, $idType, $prixUnitaire, $quantity, $quantityRestante, $libelle);
	}

	public function delete(int $id): void
	{
		$this->repository->delete($id);
	}
}

