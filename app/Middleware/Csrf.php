<?php

declare(strict_types=1);

namespace Sgdj\Middleware;

/**
 * Protección CSRF para operaciones sensibles (envíos POST).
 *
 * El token se genera una vez por sesión y se compara con el valor enviado
 * mediante hash_equals() para evitar ataques de falsificación de solicitud.
 */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function campo(): string
    {
        return '<input type="hidden" name="csrf_token" value="' .
            \esc(self::token()) .
            '">';
    }

    public static function verificar(): void
    {
        $token = $_POST['csrf_token'] ?? '';

        if (
            !is_string($token) ||
            $token === '' ||
            !hash_equals(self::token(), $token)
        ) {
            \abortar_http(
                400,
                'Token de seguridad inválido. Intente nuevamente.'
            );
        }
    }
}