<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\esc;
use function Sgdj\helpers\flash;

/**
 * Controlador de clientes.
 *
 * Acciones definidas en routes/web.php:
 *   - 'listar'     => ['GET']
 *   - 'crear'      => ['GET']
 *   - 'guardar'    => ['POST']
 *   - 'editar'     => ['GET']
 *   - 'actualizar' => ['POST']
 *   - 'eliminar'   => ['POST']
 *
 * Nota: la persistencia contra la base de datos se integra en el bloque
 * 3.1.3 cuando existan los modelos correspondientes. Por ahora, valida parámetros básicos y selecciona la vista correspondiente.
 */
final class ClientesController
{
    public function listar(): void
    {
        renderizar('clientes/lista', []);
    }

    public function crear(): void
    {
        renderizar('clientes/crear', []);
    }

    public function guardar(): void
    {
        $nombre = campo('nombre', '');
        $apellido = campo('apellido', '');
        $email = campo('email', '');

        if ($nombre === '' || $apellido === '' || $email === '') {
            flash('Todos los campos son obligatorios.');
            redirigir('clientes');
        }

        // TODO: integrar con modelo Cliente en el bloque 3.1.3.

        flash('Cliente guardado correctamente.');
        redirigir('clientes');
    }

    public function editar(): void
    {
        $id = (int)campo('id', 0);
        if ($id === 0) {
            abortar_http(400, 'ID de cliente no válido.');
        }

        renderizar('clientes/editar', ['id' => $id]);
    }

    public function actualizar(): void
    {
        $id = (int)campo('id', 0);
        $nombre = campo('nombre', '');
        $apellido = campo('apellido', '');
        $email = campo('email', '');

        if ($id === 0 || $nombre === '' || $apellido === '' || $email === '') {
            abortar_http(400, 'Datos incompletos para actualizar.');
        }

        // TODO: integrar con modelo Cliente en el bloque 3.1.3.

        flash('Cliente actualizado correctamente.');
        redirigir('clientes');
    }

    public function eliminar(): void
    {
        $id = (int)campo('id', 0);
        if ($id === 0) {
            abortar_http(400, 'ID de cliente no válido.');
        }

        // TODO: integrar con modelo Cliente en el bloque 3.1.3.

        flash('Cliente eliminado correctamente.');
        redirigir('clientes');
    }
}