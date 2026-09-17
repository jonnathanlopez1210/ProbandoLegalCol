<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Middleware\Auth;
use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\esc;

/**
 * Controlador de dashboard.
 *
 * Acciones definidas en routes/web.php:
 *   - 'mostrar' (GET /dashboard) → muestra la página principal.
 *   - Si el usuario no está autenticado, redirige al login.
 */
final class DashboardController
{
    public function mostrar(): void
    {
        if (!Auth::estaAutenticado()) {
            redirigir('login');
        }

        renderizar('dashboard/index', []);
    }
}