<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\flash;

/**
 * Controlador de documentos.
 *
 * Acciones definidas en routes/web.php:
 *   - 'listar'      => ['GET']
 *   - 'crear'       => ['GET']
 *   - 'guardar'     => ['POST']
 *   - 'editar'      => ['GET']
 *   - 'actualizar'  => ['POST']
 *   - 'eliminar'    => ['POST']
 *   - 'descargar'   => ['GET']
 *
 * Nota: la gestión de archivos contra storage/documentos/ y la BD se
 * integra en el bloque 3.1.3. Por ahora, estructura y vistas.
 */
final class DocumentosController
{
    public function listar(): void
    {
        renderizar('documentos/lista', []);
    }

    public function crear(): void
    {
        renderizar('documentos/crear', []);
    }

    public function guardar(): void
    {
        $titulo = campo('titulo', '');
        $tipo = campo('tipo', '');

        if ($titulo === '') {
            flash('El título es obligatorio.');
            redirigir('documentos');
        }

        // TODO: integrar con modelo Documento y storage/documentos/ en el bloque 3.1.3.

        flash('Documento guardado correctamente.');
        redirigir('documentos');
    }

    public function editar(): void
    {
        $id = (int)campo('id', 0);
        if ($id === 0) {
            abortar_http(400, 'ID de documento no válido.');
        }

        renderizar('documentos/editar', ['id' => $id]);
    }

    public function actualizar(): void
    {
        $id = (int)campo('id', 0);
        $titulo = campo('titulo', '');

        if ($id === 0 || $titulo === '') {
            abortar_http(400, 'Datos incompletos para actualizar.');
        }

        // TODO: integrar con modelo Documento en el bloque 3.1.3.

        flash('Documento actualizado correctamente.');
        redirigir('documentos');
    }

    public function eliminar(): void
    {
        $id = (int)campo('id', 0);
        if ($id === 0) {
            abortar_http(400, 'ID de documento no válido.');
        }

        // TODO: integrar con modelo Documento en el bloque 3.1.3.

        flash('Documento eliminado correctamente.');
        redirigir('documentos');
    }

    public function descargar(): void
    {
        // TODO: implementar descarga real en el bloque 3.1.3.
        abortar_http(501, 'Descarga de documentos pendiente de implementar en el bloque 3.1.3.');
    }
}