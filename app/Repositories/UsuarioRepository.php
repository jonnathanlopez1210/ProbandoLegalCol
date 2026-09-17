<?php

declare(strict_types=1);

namespace Sgdj\Repositories;

final class UsuarioRepository extends BaseRepository
{
    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id, u.rol_id, r.nombre AS rol_nombre, u.nombres, u.apellidos,
                    u.correo, u.password, u.estado
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.correo = :correo
             LIMIT 1'
        );
        $stmt->execute([':correo' => $correo]);

        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id, u.rol_id, r.nombre AS rol_nombre, u.nombres, u.apellidos,
                    u.correo, u.estado, u.created_at
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }

    public function listar(string $busqueda = ''): array
    {
        $sql = 'SELECT u.id, u.rol_id, r.nombre AS rol_nombre, u.nombres, u.apellidos,
                       u.correo, u.estado, u.created_at
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol_id';

        $parametros = [];

        if ($busqueda !== '') {
            $sql .= ' WHERE u.nombres LIKE :busqueda
                       OR u.apellidos LIKE :busqueda
                       OR u.correo LIKE :busqueda';
            $parametros[':busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY u.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public function abogadosActivos(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id, r.nombre AS rol_nombre, u.nombres, u.apellidos, u.correo
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE r.nombre = :rol
               AND u.estado = 1
             ORDER BY u.apellidos ASC, u.nombres ASC'
        );
        $stmt->execute([':rol' => 'Abogado']);

        return $stmt->fetchAll();
    }

    public function existeCorreo(string $correo, ?int $excluirId = null): bool
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM usuarios
                WHERE correo = :correo';

        $parametros = [':correo' => $correo];

        if ($excluirId !== null) {
            $sql .= ' AND id <> :id';
            $parametros[':id'] = $excluirId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function crear(array $datos): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (rol_id, nombres, apellidos, correo, password, estado)
             VALUES (:rol_id, :nombres, :apellidos, :correo, :password, :estado)'
        );
        $stmt->execute([
            ':rol_id'   => $datos['rol_id'],
            ':nombres'  => $datos['nombres'],
            ':apellidos' => $datos['apellidos'],
            ':correo'   => $datos['correo'],
            ':password' => $datos['password'],
            ':estado'   => $datos['estado'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios
             SET rol_id = :rol_id, nombres = :nombres, apellidos = :apellidos,
                 correo = :correo
             WHERE id = :id'
        );
        $stmt->execute([
            ':rol_id'    => $datos['rol_id'],
            ':nombres'   => $datos['nombres'],
            ':apellidos' => $datos['apellidos'],
            ':correo'    => $datos['correo'],
            ':id'        => $id,
        ]);
    }

    public function actualizarPassword(int $id, string $hash): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios SET password = :password WHERE id = :id'
        );
        $stmt->execute([
            ':password' => $hash,
            ':id'       => $id,
        ]);
    }

    public function cambiarEstado(int $id, int $estado): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuarios SET estado = :estado WHERE id = :id'
        );
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}