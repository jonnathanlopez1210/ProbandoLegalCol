<?php

declare(strict_types=1);

namespace Sgdj\Repositories;

final class DashboardRepository extends BaseRepository
{
    public function contarClientes(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM clientes WHERE estado = 1')->fetchColumn();
    }

    public function contarProcesosActivos(): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM procesos p
             INNER JOIN estados_proceso ep ON ep.id = p.estado_proceso_id
             WHERE ep.nombre <> :finalizado
               AND ep.nombre <> :archivado'
        );
        $stmt->execute([':finalizado' => 'Finalizado', ':archivado' => 'Archivado']);

        return (int) $stmt->fetchColumn();
    }

    public function contarDocumentos(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM documentos WHERE estado = 1')->fetchColumn();
    }

    public function contarUsuarios(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM usuarios WHERE estado = 1')->fetchColumn();
    }

    public function procesosPorEstado(): array
    {
        $stmt = $this->pdo->query(
            'SELECT ep.nombre AS estado_nombre, COUNT(p.id) AS total
             FROM estados_proceso ep
             LEFT JOIN procesos p ON p.estado_proceso_id = ep.id
             GROUP BY ep.id, ep.nombre
             ORDER BY ep.id'
        );

        return $stmt->fetchAll();
    }
}