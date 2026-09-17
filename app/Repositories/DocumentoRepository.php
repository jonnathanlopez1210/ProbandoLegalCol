<?php

declare(strict_types=1);

namespace Sgdj\Repositories;

final class DocumentoRepository extends BaseRepository
{
    private const SELECT_BASE = '
        SELECT d.id, d.proceso_id, d.tipo_documento_id, d.usuario_id,
               d.nombre, d.descripcion, d.nombre_archivo, d.ruta_archivo,
               d.extension, d.tamano, d.fecha_documento, d.estado,
               d.created_at, d.updated_at,
               p.numero_proceso, p.titulo AS proceso_titulo,
               td.nombre AS tipo_documento_nombre,
               u.nombres AS usuario_nombres,
               u.apellidos AS usuario_apellidos
        FROM documentos d
        INNER JOIN procesos p         ON p.id = d.proceso_id
        INNER JOIN tipos_documento td ON td.id = d.tipo_documento_id
        INNER JOIN usuarios u         ON u.id = d.usuario_id';

    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_BASE . '
            WHERE d.id = :id
            LIMIT 1'
        );
        $stmt->execute([':id' => $id]);

        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }

    public function listar(string $busqueda = '', ?int $proceso = null, ?int $tipoDocumento = null): array
    {
        $sql = self::SELECT_BASE . ' WHERE 1 = 1';

        $parametros = [];

        if ($busqueda !== '') {
            $sql .= ' AND (d.nombre LIKE :busqueda
                        OR d.descripcion LIKE :busqueda
                        OR p.numero_proceso LIKE :busqueda)';
            $parametros[':busqueda'] = '%' . $busqueda . '%';
        }

        if ($proceso !== null) {
            $sql .= ' AND d.proceso_id = :proceso';
            $parametros[':proceso'] = $proceso;
        }

        if ($tipoDocumento !== null) {
            $sql .= ' AND d.tipo_documento_id = :tipo';
            $parametros[':tipo'] = $tipoDocumento;
        }

        $sql .= ' ORDER BY d.updated_at DESC, d.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public function listarPorProceso(int $procesoId): array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_BASE . '
            WHERE d.proceso_id = :proceso
            ORDER BY d.fecha_documento DESC, d.id DESC'
        );
        $stmt->execute([':proceso' => $procesoId]);

        return $stmt->fetchAll();
    }

    public function crear(array $datos): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO documentos
                (proceso_id, tipo_documento_id, usuario_id, nombre, descripcion,
                 nombre_archivo, ruta_archivo, extension, tamano, fecha_documento, estado)
             VALUES
                (:proceso_id, :tipo_documento_id, :usuario_id, :nombre, :descripcion,
                 :nombre_archivo, :ruta_archivo, :extension, :tamano, :fecha_documento, :estado)'
        );
        $stmt->execute([
            ':proceso_id'       => $datos['proceso_id'],
            ':tipo_documento_id' => $datos['tipo_documento_id'],
            ':usuario_id'       => $datos['usuario_id'],
            ':nombre'           => $datos['nombre'],
            ':descripcion'      => $datos['descripcion'],
            ':nombre_archivo'   => $datos['nombre_archivo'],
            ':ruta_archivo'     => $datos['ruta_archivo'],
            ':extension'        => $datos['extension'],
            ':tamano'           => $datos['tamano'],
            ':fecha_documento'  => $datos['fecha_documento'],
            ':estado'           => 1,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function actualizar(int $id, array $datos): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE documentos
             SET proceso_id = :proceso_id,
                 tipo_documento_id = :tipo_documento_id,
                 nombre = :nombre,
                 descripcion = :descripcion,
                 fecha_documento = :fecha_documento
             WHERE id = :id'
        );
        $stmt->execute([
            ':proceso_id'       => $datos['proceso_id'],
            ':tipo_documento_id' => $datos['tipo_documento_id'],
            ':nombre'           => $datos['nombre'],
            ':descripcion'      => $datos['descripcion'],
            ':fecha_documento'  => $datos['fecha_documento'],
            ':id'               => $id,
        ]);
    }

    public function buscarLimitado(string $busqueda, int $limite): array
    {
        $limite = max(1, $limite);

        $stmt = $this->pdo->prepare(
            'SELECT d.id, d.nombre, d.descripcion, d.estado
             FROM documentos d
             WHERE d.nombre LIKE :busqueda
                OR d.descripcion LIKE :busqueda
             ORDER BY d.updated_at DESC
             LIMIT ' . $limite
        );
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}