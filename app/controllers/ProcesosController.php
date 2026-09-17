<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Models\Proceso;
use Sgdj\Models\Cliente;
use Sgdj\Repositories\TipoProcesoRepository;
use Sgdj\Repositories\EstadoProcesoRepository;
use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\esc;
use function Sgdj\helpers\flash;

/**
 * Controlador de procesos.
 *
 * Acciones definidas en routes/web.php:
 *   - 'listar'     => ['GET']
 *   - 'crear'      => ['GET']
 *   - 'guardar'    => ['POST']
 *   - 'editar'     => ['GET']
 *   - 'actualizar' => ['POST']
 *   - 'eliminar'   => ['POST']
 *
 * El controlador usa el modelo `Proceso` para operar contra la base de datos.
 */
final class ProcesosController
{
    public function listar(): void
    {
        $procesos = Proceso::listar();
        renderizar('procesos/lista', ['procesos' => $procesos]);
    }

    public function crear(): void
    {
        $pdo = \Sgdj\Database\Conexion::obtener();
        $tipoProcesoRepo = new TipoProcesoRepository($pdo);
        $estadoProcesoRepo = new EstadoProcesoRepository($pdo);
        
        $clientes = Cliente::listar(1); // Solo activos
        $tiposProceso = $tipoProcesoRepo->listarActivos();
        $estadosProceso = $estadoProcesoRepo->listarActivos();
        
        renderizar('procesos/crear', [
            'clientes' => $clientes,
            'tiposProceso' => $tiposProceso,
            'estadosProceso' => $estadosProceso
        ]);
    }

    public function guardar(): void
    {
        $resultado = Proceso::crear([
            'cliente_id'          => (int)($_POST['cliente_id'] ?? 0),
            'tipo_proceso_id'     => (int)($_POST['tipo_proceso_id'] ?? 0),
            'estado_proceso_id'   => (int)($_POST['estado_proceso_id'] ?? 0),
            'responsable_id'      => (int)($_POST['responsable_id'] ?? 0),
            'numero_proceso'      => $_POST['numero_proceso'] ?? '',
            'titulo'              => $_POST['titulo'] ?? '',
            'descripcion'         => $_POST['descripcion'] ?? '',
            'fecha_inicio'        => $_POST['fecha_inicio'] ?? '',
            'fecha_finalizacion'  => $_POST['fecha_finalizacion'] ?? '',
        ]);

        if ($resultado === false) {
            // Puede ser número de proceso duplicado o FK inexistente
            flash('error', 'No es posible guardar el proceso: verifique los datos e intente nuevamente.');
            redirigir('procesos');
        }

        flash('exito', 'Proceso guardado correctamente.');
        redirigir('procesos');
    }

    public function editar(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de proceso no válido.');
        }

        $proceso = Proceso::obtenerPorId($id);

        if ($proceso === false) {
            abortar_http(404, 'El proceso no existe.');
        }

        $pdo = \Sgdj\Database\Conexion::obtener();
        $tipoProcesoRepo = new TipoProcesoRepository($pdo);
        $estadoProcesoRepo = new EstadoProcesoRepository($pdo);
        
        $clientes = Cliente::listar(1);
        $tiposProceso = $tipoProcesoRepo->listarActivos();
        $estadosProceso = $estadoProcesoRepo->listarActivos();

        renderizar('procesos/editar', [
            'proceso' => $proceso,
            'clientes' => $clientes,
            'tiposProceso' => $tiposProceso,
            'estadosProceso' => $estadosProceso
        ]);
    }

    public function actualizar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de proceso no válido.');
        }

        $resultado = Proceso::actualizar($id, [
            'cliente_id'          => (int)($_POST['cliente_id'] ?? 0),
            'tipo_proceso_id'     => (int)($_POST['tipo_proceso_id'] ?? 0),
            'estado_proceso_id'   => (int)($_POST['estado_proceso_id'] ?? 0),
            'responsable_id'      => (int)($_POST['responsable_id'] ?? 0),
            'numero_proceso'      => $_POST['numero_proceso'] ?? '',
            'titulo'              => $_POST['titulo'] ?? '',
            'descripcion'         => $_POST['descripcion'] ?? '',
            'fecha_inicio'        => $_POST['fecha_inicio'] ?? '',
            'fecha_finalizacion'  => $_POST['fecha_finalizacion'] ?? '',
        ]);

        if ($resultado === false) {
            flash('error', 'No fue posible actualizar el proceso.');
            redirigir('procesos');
        }

        flash('exito', 'Proceso actualizado correctamente.');
        redirigir('procesos');
    }

    public function eliminar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de proceso no válido.');
        }

        // Por ahora, cambiar estado a "Archivado" (4) en lugar de eliminar físicamente
        $resultado = Proceso::cambiarEstado($id, 4);

        if ($resultado === false) {
            flash('error', 'No fue posible cambiar el estado del proceso.');
            redirigir('procesos');
        }

        flash('exito', 'Proceso Archivado correctamente.');
        redirigir('procesos');
    }
}