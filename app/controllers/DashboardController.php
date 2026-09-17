<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Middleware\Auth;

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
            \redirigir('login');
        }

        \renderizar('dashboard/index', []);
    }
}