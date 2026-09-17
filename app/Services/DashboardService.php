<?php

declare(strict_types=1);

namespace Sgdj\Services;

use Sgdj\Repositories\ClienteRepository;
use Sgdj\Repositories\DocumentoRepository;
use Sgdj\Repositories\EstadoProcesoRepository;
use Sgdj\Repositories\ProcesoRepository;

/**
 * Agrupa los indicadores que se muestran en el tablero principal.
 */
final class DashboardService
{
    public function __construct(
        private readonly ClienteRepository     $clienteRepository,
        private readonly ProcesoRepository     $procesoRepository,
        private readonly DocumentoRepository   $documentoRepository,
        private readonly EstadoProcesoRepository $estadoProcesoRepository
    ) {
    }

    public function obtenerResumen(): array
    {
        $procesos = $this->procesoRepository->listar();
        $totalProcesos = count($procesos);

        $estadosProceso = $this->estadoProcesoRepository->listarActivos();
        $procesosPorEstado = [];

        foreach ($estadosProceso as $estado) {
            $procesosPorEstado[$estado['nombre']] = 0;
        }

        foreach ($procesos as $proceso) {
            if (isset($procesosPorEstado[$proceso['estado_proceso_nombre']])) {
                $procesosPorEstado[$proceso['estado_proceso_nombre']]++;
            }
        }

        return [
            'clientes'            => $this->clienteRepository->contarActivos(),
            'procesos'            => $totalProcesos,
            'documentos'          => $this->documentoRepository->contarActivos(),
            'procesos_por_estado' => $procesosPorEstado,
        ];
    }
}