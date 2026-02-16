<?php
namespace app\services;

use app\repository\VilleRepository;
use flight\util\Collection;

class VilleService
{
	private VilleRepository $repository;

	public function __construct(VilleRepository $repository)
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

	public function create(string $nom, int $nombreSinistres): int
	{
		return $this->repository->create($nom, $nombreSinistres);
	}

	public function update(int $id, string $nom, int $nombreSinistres): void
	{
		$this->repository->update($id, $nom, $nombreSinistres);
	}

	public function delete(int $id): void
	{
		$this->repository->delete($id);
	}
}

