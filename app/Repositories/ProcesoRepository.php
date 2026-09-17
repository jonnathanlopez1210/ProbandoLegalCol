<?php

declare(strict_types=1);

namespace Sgdj\Repositories;

final class ProcesoRepository extends BaseRepository
{
    private const SELECT_BASE = '
        SELECT p.id, p.cliente_id, p.tipo_proceso_id, p.estado_proceso_id,
               p.responsable_id, p.numero_proceso, p.titulo, p.descripcion,
               p.fecha_inicio, p.fecha_finalizacion, p.created_at, p.updated_at,
               c.tipo_cliente,
               c.nombres    AS cliente_nombres,
               c.apellidos  AS cliente_apellidos,
               c.razon_social AS cliente_razon_social,
               tp.nombre    AS tipo_proceso_nombre,
               ep.nombre    AS estado_proceso_nombre,
               u.nombres    AS responsable_nombres,
               u.apellidos  AS responsable_apellidos
        FROM procesos p
        INNER JOIN clientes c        ON c.id = p.cliente_id
        INNER JOIN tipos_proceso tp  ON tp.id = p.tipo_proceso_id
        INNER JOIN estados_proceso ep ON ep.id = p.estado_proceso_id
        INNER JOIN usuarios u        ON u.id = p.responsable_id';

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_BASE . '
            WHERE p.id = :id
            LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }

    public function listar(string $busqueda = '', ?int $tipoProceso = null, ?int $estadoProceso = null): array
    {
        $sql = self::SELECT_BASE . ' WHERE 1 = 1';

        $parametros = [];

        if ($busqueda !== '') {
            $sql .= ' AND (p.numero_proceso LIKE :busqueda
                        OR p.titulo LIKE :busqueda
                        OR p.descripcion LIKE :busqueda)';
            $parametros[':busqueda'] = '%' . $busqueda . '%';
        }

        if ($tipoProceso !== null) {
            $sql .= ' AND p.tipo_proceso_id = :tipo';
            $parametros[':tipo'] = $tipoProceso;
        }

        if ($estadoProceso !== null) {
            $sql .= ' AND p.estado_proceso_id = :estado';
            $parametros[':estado'] = $estadoProceso;
        }

        $sql .= ' ORDER BY p.updated_at DESC, p.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public function crear(array $datos): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO procesos
                (cliente_id, tipo_proceso_id, estado_proceso_id, responsable_id,
                 numero_proceso, titulo, descripcion, fecha_inicio, fecha_finalizacion)
             VALUES
                (:cliente_id, :tipo_proceso_id, :estado_proceso_id, :responsable_id,
                 :numero_proceso, :titulo, :descripcion, :fecha_inicio, :fecha_finalizacion)'
        );
        $stmt->execute([
            ':cliente_id'         => $datos['cliente_id'],
            ':tipo_proceso_id'    => $datos['tipo_proceso_id'],
            ':estado_proceso_id'  => $datos['estado_proceso_id'],
            ':responsable_id'     => $datos['responsable_id'],
            ':numero_proceso'     => $datos['numero_proceso'],
            ':titulo'             => $datos['titulo'],
            ':descripcion'        => $datos['descripcion'],
            ':fecha_inicio'       => $datos['fecha_inicio'],
            ':fecha_finalizacion' => $datos['fecha_finalizacion'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE procesos
             SET cliente_id = :cliente_id,
                 tipo_proceso_id = :tipo_proceso_id,
                 responsable_id = :responsable_id,
                 numero_proceso = :numero_proceso,
                 titulo = :titulo,
                 descripcion = :descripcion,
                 fecha_inicio = :fecha_inicio
             WHERE id = :id'
        );
        $stmt->execute([
            ':cliente_id'        => $datos['cliente_id'],
            ':tipo_proceso_id'   => $datos['tipo_proceso_id'],
            ':responsable_id'    => $datos['responsable_id'],
            ':numero_proceso'    => $datos['numero_proceso'],
            ':titulo'            => $datos['titulo'],
            ':descripcion'       => $datos['descripcion'],
            ':fecha_inicio'      => $datos['fecha_inicio'],
            ':id'                => $id,
        ]);
    }

    public function existeNumeroProceso(string $numero, ?int $excluirId = null): bool
    {
        $sql = 'SELECT COUNT(*) AS total
                FROM procesos
                WHERE numero_proceso = :numero';

        $parametros = [':numero' => $numero];

        if ($excluirId !== null) {
            $sql .= ' AND id <> :id';
            $parametros[':id'] = $excluirId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function cambiarEstado(int $id, int $estadoProcesoId, ?string $fechaFinalizacion): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE procesos
             SET estado_proceso_id = :estado_proceso_id,
                 fecha_finalizacion = :fecha_finalizacion
             WHERE id = :id'
        );
        $stmt->execute([
            ':estado_proceso_id'  => $estadoProcesoId,
            ':fecha_finalizacion' => $fechaFinalizacion,
            ':id'                 => $id,
        ]);
    }

    public function buscarLimitado(string $busqueda, int $limite): array
    {
        $limite = max(1, $limite);

        $stmt = $this->pdo->prepare(
            'SELECT id, numero_proceso, titulo, descripcion, estado_proceso_id
             FROM procesos
             WHERE numero_proceso LIKE :busqueda
                OR titulo LIKE :busqueda
                OR descripcion LIKE :busqueda
             ORDER BY updated_at DESC
             LIMIT ' . $limite
        );
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}