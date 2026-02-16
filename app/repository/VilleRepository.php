<?php
namespace app\repository;

use flight\database\PdoWrapper;
use flight\util\Collection;

class VilleRepository
{
    private PdoWrapper $pdo;

    public function __construct(PdoWrapper $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        return $this->pdo->fetchAll(
            'SELECT id, nom, nombre_sinistres FROM BNR_ville ORDER BY id DESC'
        );
    }

    public function find(int $id): ?Collection
    {
        $row = $this->pdo->fetchRow(
            'SELECT id, nom, nombre_sinistres FROM BNR_ville WHERE id = ?',
            [$id]
        );

        return $row instanceof Collection && $row->count() > 0 ? $row : null;
    }

    public function create(string $nom, int $nombreSinistres): int
    {
        $this->pdo->runQuery(
            'INSERT INTO BNR_ville (nom, nombre_sinistres) VALUES (?, ?)',
            [$nom, $nombreSinistres]
        );

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $nom, int $nombreSinistres): void
    {
        $this->pdo->runQuery(
            'UPDATE BNR_ville SET nom = ?, nombre_sinistres = ? WHERE id = ?',
            [$nom, $nombreSinistres, $id]
        );
    }

    public function delete(int $id): void
    {
        $this->pdo->runQuery(
            'DELETE FROM BNR_ville WHERE id = ?',
            [$id]
        );
    }
}

