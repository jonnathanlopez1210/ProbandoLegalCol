<?php

declare(strict_types=1);

namespace Sgdj\Repositories;

final class RolRepository extends BaseRepository
{
    public function listarActivos(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nombre, descripcion
             FROM roles
             WHERE estado = 1
             ORDER BY id ASC'
        );

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, descripcion, estado
             FROM roles
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }
}