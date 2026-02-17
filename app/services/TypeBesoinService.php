<?php
namespace app\services;

use app\repository\TypeBesoinRepository;
use flight\util\Collection;

class TypeBesoinService
{
  private TypeBesoinRepository $repository;

  public function __construct(TypeBesoinRepository $repository)
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

  public function create(string $libelle): int
  {
    return $this->repository->create($libelle);
  }

  public function update(int $id, string $libelle): void
  {
    $this->repository->update($id, $libelle);
  }

  public function delete(int $id): void
  {
    $this->repository->delete($id);
  }
}

