<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Middleware\Auth;
use Sgdj\Middleware\Csrf;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\esc;
use function Sgdj\helpers\abortar_http;

/**
 * Controlador de autenticación.
 *
 * Soporta las dos acciones definidas en routes/web.php:
 *   - 'mostrar' (GET /login)  → muestra el formulario de login
 *   - 'iniciar' (POST /login) → procesa el formulario de login
 */
final class AuthController
{
    public function mostrar(): void
    {
        renderizar('auth/login', []);
    }

    public function iniciar(): void
    {
        // Validación básica de parámetros de entrada.
        // Nota: la validación contra la base de datos y el modelo de usuario
        // se completará en el siguiente bloque (3.1.3) cuando estén disponibles.
        // Por ahora, se prepara la estructura y se documenta el pendiente.

        $usuario = campo('usuario', '');
        $contrasena = campo('contrasena', '');

        if ($usuario === '' || $contrasena === '') {
            flash('Los campos usuario y contraseña son obligatorios.');
            redirigir('login');
        }

        // TODO: cuando existan los modelos de usuario, descomentar y usar:
        // $usuarioModel = new UsuariosModelo();
        // $usuarioValidado = $usuarioModel->autenticar($usuario, $contrasena);
        // if ($usuarioValidado) {
        //     Auth::iniciarSesion(['id' => $usuarioValidado['id'], ...]);
        //     redirigir('dashboard');
        // } else {
        //     flash('Usuario o contraseña incorrectos.');
        //     redirigir('login');
        // }

        // Placeholder: credenciales incorrectas.
        flash('Credenciales no válidas. Por favor, inténtelo de nuevo.');
        redirigir('login');
    }
}