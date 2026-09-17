<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Models\Documento;
use Sgdj\Models\Proceso;
use Sgdj\Models\TipoDocumento;
use function Sgdj\helpers\redirect;
use function Sgdj\helpers\appUrl;

final class BusquedaController
{
    /**
     * Muestra el formulario de búsqueda y procesa los filtros.
     *
     * Si la petición es POST con parámetros de búsqueda, carga los resultados
     * y los pasa a la vista. Si es GET, muestra el formulario vacío.
     *
     * @return void
     */
    public function index(): void
    {
        // Cargar datos para los filtros del formulario
        $procesos = Proceso::listar();
        $tiposDocumento = TipoDocumento::listar();

        $numeroProceso = null;
        $nombre = null;
        $tipoDocumentoId = null;
        $estado = null;
        $resultados = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y sanitizar los parámetros del formulario
            $numeroProceso = $_POST['numero_proceso'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $tipoDocumentoId = $_POST['tipo_documento_id'] ?? null;
            $estado = $_POST['estado'] ?? null;

            // Asegurar que sean tipos adecuados
            if ($numeroProceso !== '') {
                $numeroProceso = trim($numeroProceso);
            }
            if ($nombre !== '') {
                $nombre = trim($nombre);
            }
            if ($tipoDocumentoId !== null) {
                $tipoDocumentoId = (int) $tipoDocumentoId;
            }
            if ($estado !== null) {
                $estado = (int) $estado;
            }

            // Ejecutar búsqueda
            $resultados = Documento::buscar(
                $numeroProceso !== '' ? $numeroProceso : null,
                $nombre !== '' ? $nombre : null,
                $tipoDocumentoId,
                $estado
            );
        }

        // Renderizar la vista pasando los datos del formulario y los resultados
        $this->renderizar($procesos, $tiposDocumento, $numeroProceso, $nombre,
            $tipoDocumentoId, $estado, $resultados);
    }

    /**
     * Renderiza la vista de búsqueda con los datos proporcionados.
     *
     * @param array $procesos Lista de procesos para el filtro desplegable
     * @param array $tiposDocumento Lista de tipos de documento para el filtro desplegable
     * @param string $numeroProceso Valor actual del filtro número de proceso
     * @param string $nombre Valor actual del filtro nombre
     * @param int|null $tipoDocumentoId Valor actual del filtro tipo documento
     * @param int|null $estado Valor actual del filtro estado
     * @param array $resultados Resultados de la búsqueda (puede estar vacío)
     * @return void
     */
    private function renderizar(array $procesos, array $tiposDocumento,
        string $numeroProceso, string $nombre,
        ?int $tipoDocumentoId, ?int $estado, array $resultados): void
    {
        // Preparar datos para la vista
        $datosVista = [
            'procesos'          => $procesos,
            'tipos_documento'   => $tiposDocumento,
            'numero_proceso'    => $numeroProceso,
            'nombre'            => $nombre,
            'tipo_documento_id' => $tipoDocumentoId,
            'estado'            => $estado,
            'resultados'        => $resultados,
        ];

        // Renderizar la vista
        $this->ver('busqueda/index', $datosVista);
    }

    /**
     * Alias para renderizar vistas. Delega en Bootstrap.
     *
     * @param string $nombreVista Nombre del archivo de vista (sin extensión .php)
     * @param array $datos Datos a pasar a la vista
     * @return void
     */
    private function ver(string $nombreVista, array $datos): void
    {
        \renderizar('busqueda/index', $datos);
    }
}