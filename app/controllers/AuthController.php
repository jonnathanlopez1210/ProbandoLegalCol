<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Models\Usuario;

/**
 * Controlador de autenticación.
 *
 * Soporta las acciones definidas en routes/web.php:
 *   - 'mostrar' (GET /login)  → muestra el formulario de login
 *   - 'iniciar' (POST /login) → procesa el formulario de login
 */
final class AuthController
{
    public function mostrar(): void
    {
        \renderizar('auth/login', []);
    }

    public function iniciar(): void
    {
        $correo = $_POST['usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';

        if ($correo === '' || $contrasena === '') {
            \flash('error', 'Los campos usuario y contraseña son obligatorios.');
            \redirigir('login');
        }

        // 1. Buscar usuario por correo en la BD
        $usuario = Usuario::buscarPorCorreo($correo);

        if ($usuario === false) {
            // No revelar si el usuario existe o no
            \flash('error', 'Credenciales no válidas. Por favor, inténtelo de nuevo.');
            \redirigir('login');
        }

        // 2. Verificar contraseña con password_verify()
        if (!password_verify($contrasena, $usuario['password'])) {
            // No revelar si la contraseña es incorrecta o el usuario no existe
            \flash('error', 'Credenciales no válidas. Por favor, inténtelo de nuevo.');
            \redirigir('login');
        }

        // 3. Verificar que el usuario esté activo
        if ((int) $usuario['estado'] !== 1) {
            \flash('error', 'La cuenta no está activa.');
            \redirigir('login');
        }

        // 4. Crear sesión autenticada
        $rolId = $usuario['rol_id'];

        \Sgdj\Middleware\Auth::iniciarSesion([
            'id'         => $usuario['id'],
            'rol_id'     => $rolId,
            'rol_nombre' => $rolId === 1 ? 'Administrador' : 'Abogado',
            'nombres'    => $usuario['nombres'],
            'apellidos'  => $usuario['apellidos'],
            'correo'     => $usuario['correo'],
        ]);

        // 5. Redirigir al dashboard
        \redirigir('dashboard');
    }

    public function cerrar(): void
    {
        \Sgdj\Middleware\Auth::cerrar();

        \redirigir('login');
    }
}