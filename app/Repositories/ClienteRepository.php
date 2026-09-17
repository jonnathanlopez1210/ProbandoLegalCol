<?php

declare(strict_types=1);

namespace Sgdj\Repositories;

final class ClienteRepository extends BaseRepository
{
    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM clientes
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }

    public function listar(string $busqueda = '', ?int $estado = null): array
    {
        $sql = 'SELECT *
                FROM clientes
                WHERE 1 = 1';

        $parametros = [];

        if ($busqueda !== '') {
            $sql .= ' AND (nombres LIKE :busqueda
                        OR apellidos LIKE :busqueda
                        OR razon_social LIKE :busqueda
                        OR numero_identificacion LIKE :busqueda)';
            $parametros[':busqueda'] = '%' . $busqueda . '%';
        }

        if ($estado !== null) {
            $sql .= ' AND estado = :estado';
            $parametros[':estado'] = $estado;
        }

        $sql .= ' ORDER BY updated_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public function existeIdentificacion(string $tipo, string $numero, ?int $excluirId = null): bool
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM clientes
                WHERE tipo_identificacion = :tipo
                  AND numero_identificacion = :numero';

        $parametros = [':tipo' => $tipo, ':numero' => $numero];

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
            'INSERT INTO clientes
                (tipo_cliente, tipo_identificacion, numero_identificacion,
                 nombres, apellidos, razon_social, correo, telefono, direccion, estado)
             VALUES
                (:tipo_cliente, :tipo_identificacion, :numero_identificacion,
                 :nombres, :apellidos, :razon_social, :correo, :telefono, :direccion, :estado)'
        );
        $stmt->execute([
            ':tipo_cliente'         => $datos['tipo_cliente'],
            ':tipo_identificacion'  => $datos['tipo_identificacion'],
            ':numero_identificacion' => $datos['numero_identificacion'],
            ':nombres'              => $datos['nombres'],
            ':apellidos'            => $datos['apellidos'],
            ':razon_social'         => $datos['razon_social'],
            ':correo'               => $datos['correo'],
            ':telefono'             => $datos['telefono'],
            ':direccion'            => $datos['direccion'],
            ':estado'               => $datos['estado'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE clientes
             SET tipo_cliente = :tipo_cliente,
                 tipo_identificacion = :tipo_identificacion,
                 numero_identificacion = :numero_identificacion,
                 nombres = :nombres,
                 apellidos = :apellidos,
                 razon_social = :razon_social,
                 correo = :correo,
                 telefono = :telefono,
                 direccion = :direccion
             WHERE id = :id'
        );
        $stmt->execute([
            ':tipo_cliente'         => $datos['tipo_cliente'],
            ':tipo_identificacion'  => $datos['tipo_identificacion'],
            ':numero_identificacion' => $datos['numero_identificacion'],
            ':nombres'              => $datos['nombres'],
            ':apellidos'            => $datos['apellidos'],
            ':razon_social'         => $datos['razon_social'],
            ':correo'               => $datos['correo'],
            ':telefono'             => $datos['telefono'],
            ':direccion'            => $datos['direccion'],
            ':id'                   => $id,
        ]);
    }

    public function cambiarEstado(int $id, int $estado): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE clientes SET estado = :estado WHERE id = :id'
        );
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public function buscarLimitado(string $busqueda, int $limite): array
    {
        $limite = max(1, $limite);

        $stmt = $this->pdo->prepare(
            'SELECT id, tipo_cliente, tipo_identificacion, numero_identificacion,
                    nombres, apellidos, razon_social
             FROM clientes
             WHERE nombres LIKE :busqueda
                OR apellidos LIKE :busqueda
                OR razon_social LIKE :busqueda
                OR numero_identificacion LIKE :busqueda
             ORDER BY updated_at DESC
             LIMIT ' . $limite
        );
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}