<?php

declare(strict_types=1);

namespace Sgdj\Core;

use Sgdj\Middleware\Auth;
use Sgdj\Middleware\Csrf;

/**
 * Enrutador del front controller.
 *
 * Resuelve la ruta solicitada (?ruta=X&accion=Y) contra la tabla definida en
 * routes/web.php, valida el método HTTP y el token CSRF en envíos POST,
 * y despacha la acción del controlador correspondiente.
 */
final class Router
{
    private array $rutas;

    public function __construct(array $rutas)
    {
        $this->rutas = $rutas;
    }

    public function despachar(): void
    {
        $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $ruta   = normalizar($_GET['ruta'] ?? '');
        $accion = normalizar($_GET['accion'] ?? '');

        /*
        * Si no se especifica una ruta, se determina la ruta y acción
        * iniciales según el estado de autenticación.
        */
        if ($ruta === '') {
            $rutaPorDefecto = $this->rutaPorDefecto();

            $ruta   = $rutaPorDefecto['ruta'];
            $accion = $accion === '' ? $rutaPorDefecto['accion'] : $accion;
        }

        if (!isset($this->rutas[$ruta])) {
            abortar_http(404, 'La página solicitada no existe.');
        }

        $definicion = $this->rutas[$ruta];

        if (!isset($definicion['acciones'][$accion])) {
            abortar_http(404, 'La acción solicitada no existe.');
        }

        $metodosPermitidos = $definicion['acciones'][$accion];

        if (!in_array($metodo, $metodosPermitidos, true)) {
            abortar_http(405, 'Método no permitido para esta operación.');
        }

        if ($metodo === 'POST') {
            Csrf::verificar();
        }

        $clase = $definicion['controlador'];
        $controlador = new $clase();
        $controlador->{$accion}();
    }

    private function rutaPorDefecto(): array
    {
        if (Auth::estaAutenticado()) {
            return [
                'ruta'   => 'dashboard',
                'accion' => 'mostrar',
            ];
        }

        return [
            'ruta'   => 'login',
            'accion' => 'mostrar',
        ];
    }
}