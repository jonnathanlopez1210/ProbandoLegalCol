<?php

declare(strict_types=1);

namespace Sgdj\Middleware;

/**
 * Autenticación y autorización basadas en sesión.
 *
 * La sesión autenticada almacena: id, rol_id, rol_nombre, nombres,
 * apellidos y correo del usuario.
 */
final class Auth
{
    public static function estaAutenticado(): bool
    {
        return isset($_SESSION['usuario']['id']);
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function usuarioId(): int
    {
        return (int) ($_SESSION['usuario']['id'] ?? 0);
    }

    /**
     * Establece la sesión tras una autenticación exitosa, renovando el id
     * de sesión para evitar fijación de sesión.
     */
    public static function iniciarSesion(array $usuario): void
    {
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id'         => (int) $usuario['id'],
            'rol_id'     => (int) $usuario['rol_id'],
            'rol_nombre' => (string) $usuario['rol_nombre'],
            'nombres'    => (string) $usuario['nombres'],
            'apellidos'  => (string) $usuario['apellidos'],
            'correo'     => (string) $usuario['correo'],
        ];
    }

    public static function requireLogin(): void
    {
        if (!self::estaAutenticado()) {
            redirigir('login');
        }
    }

    public static function requireRol(string ...$rolesPermitidos): void
    {
        $usuario = self::usuario();

        if ($usuario === null) {
            redirigir('login');
        }

        if (!in_array($usuario['rol_nombre'], $rolesPermitidos, true)) {
            abortar_http(403, 'No tiene permisos para realizar esta operación.');
        }
    }

    public static function cerrar(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}