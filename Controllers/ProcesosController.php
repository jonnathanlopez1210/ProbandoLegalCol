<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
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
 * Nota: la persistencia se integra en el bloque 3.1.3 cuando existan
 * los modelos correspondientes. Por ahora, validación básica y vistas.
 */
final class ProcesosController
{
    public function listar(): void
    {
        renderizar('procesos/lista', []);
    }

    public function crear(): void
    {
        renderizar('procesos/crear', []);
    }

    public function guardar(): void
    {
        $nombre = campo('nombre', '');
        $descripcion = campo('descripcion', '');

        if ($nombre === '') {
            flash('El nombre es obligatorio.');
            redirigir('procesos');
        }

        // TODO: integrar con modelo Proceso en el bloque 3.1.3.

        flash('Proceso guardado correctamente.');
        redirigir('procesos');
    }

    public function editar(): void
    {
        $id = (int)campo('id', 0);
        if ($id === 0) {
            abortar_http(400, 'ID de proceso no válido.');
        }

        renderizar('procesos/editar', ['id' => $id]);
    }

    public function actualizar(): void
    {
        $id = (int)campo('id', 0);
        $nombre = campo('nombre', '');
        $descripcion = campo('descripcion', '');

        if ($id === 0 || $nombre === '') {
            abortar_http(400, 'Datos incompletos para actualizar.');
        }

        // TODO: integrar con modelo Proceso en el bloque 3.1.3.

        flash('Proceso actualizado correctamente.');
        redirigir('procesos');
    }

    public function eliminar(): void
    {
        $id = (int)campo('id', 0);
        if ($id === 0) {
            abortar_http(400, 'ID de proceso no válido.');
        }

        // TODO: integrar con modelo Proceso en el bloque 3.1.3.

        flash('Proceso eliminado correctamente.');
        redirigir('procesos');
    }
}