<?php

declare(strict_types=1);

namespace Sgdj\Core;

/**
 * Arranque de la aplicación.
 *
 * Registra el autoloader de la raíz de nombres Sgdj\, configura el entorno,
 * inicia la sesión y delega la solicitud al router.
 */
final class Bootstrap
{
    public static function iniciar(): void
    {
        self::cargarConfiguracion();
        self::registrarAutoloader();
        self::configurarEntorno();
        self::configurarSesion();
        self::despachar();
    }

    private static function cargarConfiguracion(): void
    {
        require_once ROOT_PATH . '/config/database.php';
        require_once ROOT_PATH . '/config/conexion.php';
    }

    private static function registrarAutoloader(): void
    {
        spl_autoload_register(static function (string $clase): void {
            $prefijo = 'Sgdj\\';

            if (!str_starts_with($clase, $prefijo)) {
                return;
            }

            $relativa = substr($clase, strlen($prefijo));

            $archivo = ROOT_PATH . '/app/' . str_replace('\\', '/', $relativa) . '.php';

            if (is_file($archivo)) {
                require_once $archivo;
            }
        });
    }


    private static function configurarEntorno(): void
    {
        date_default_timezone_set('America/Bogota');

        if (APP_ENV === 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            return;
        }

        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
        ini_set('display_errors', '0');
    }

    private static function configurarSesion(): void
    {
        ini_set('session.use_strict_mode', '1');

        session_name('sgdj_sesion');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    private static function despachar(): void
    {
        $rutas = require ROOT_PATH . '/routes/web.php';
        (new Router($rutas))->despachar();
    }
}