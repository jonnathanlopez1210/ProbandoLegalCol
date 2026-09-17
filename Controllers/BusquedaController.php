<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;

/**
 * Controlador de búsqueda.
 *
 * Acciones definidas en routes/web.php:
 *   - 'mostrar' => ['GET']
 *
 * Muestra el formulario de búsqueda. La lógica de procesamiento de la
 * consulta se completará en el bloque 3.1.3 cuando existan los modelos.
 */
final class BusquedaController
{
    public function mostrar(): void
    {
        renderizar('busqueda/index', []);
    }
}