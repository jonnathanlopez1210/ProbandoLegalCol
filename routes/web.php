<?php

declare(strict_types=1);

use Sgdj\Controllers\AuthController;
use Sgdj\Controllers\DashboardController;
use Sgdj\Controllers\ClientesController;
use Sgdj\Controllers\ProcesosController;
use Sgdj\Controllers\DocumentosController;
use Sgdj\Controllers\BusquedaController;
use Sgdj\Controllers\UsuariosController;

/**
 * Tabla de rutas de la aplicación.
 *
 * Cada entrada define la ruta (?ruta=X), el controlador que la atiende y sus
 * acciones (?accion=Y) con los métodos HTTP permitidos. Esta tabla es la que
 * consume el front controller (app/Core/Router.php) para despachar la solicitud.
 *
 * Estructura por ruta:
 *   'login' => [
 *       'controlador' => FQCN del controlador,
 *       'acciones'    => [
 *           'mostrar' => ['GET'],
 *           'iniciar' => ['POST'],
 *       ],
 *   ]
 *
 * Nota de Fase 3.1.1: los controladores referenciados se incorporan en el
 * siguiente bloque aprobado (3.1.2). Aquí solo se define el contrato de
 * enrutamiento.
 */
return [

    'login' => [
        'controlador' => AuthController::class,
        'acciones'    => [
            'mostrar' => ['GET'],
            'iniciar' => ['POST'],
        ],
    ],

    'salir' => [
        'controlador' => AuthController::class,
        'acciones'    => [
            'cerrar' => ['POST'],
        ],
    ],

    'dashboard' => [
        'controlador' => DashboardController::class,
        'acciones'    => [
            'mostrar' => ['GET'],
        ],
    ],

    'clientes' => [
        'controlador' => ClientesController::class,
        'acciones'    => [
            'listar'     => ['GET'],
            'crear'      => ['GET'],
            'guardar'    => ['POST'],
            'editar'     => ['GET'],
            'actualizar' => ['POST'],
            'eliminar'   => ['POST'],
        ],
    ],

    'procesos' => [
        'controlador' => ProcesosController::class,
        'acciones'    => [
            'listar'     => ['GET'],
            'crear'      => ['GET'],
            'guardar'    => ['POST'],
            'editar'     => ['GET'],
            'actualizar' => ['POST'],
            'eliminar'   => ['POST'],
        ],
    ],

    'documentos' => [
        'controlador' => DocumentosController::class,
        'acciones'    => [
            'listar'     => ['GET'],
            'crear'      => ['GET'],
            'guardar'    => ['POST'],
            'editar'     => ['GET'],
            'actualizar' => ['POST'],
            'eliminar'   => ['POST'],
            'descargar'  => ['GET'],
        ],
    ],

    'busqueda' => [
        'controlador' => BusquedaController::class,
        'acciones'    => [
            'mostrar' => ['GET'],
            'buscar'  => ['POST'],
        ],
    ],

    'usuarios' => [
        'controlador' => UsuariosController::class,
        'acciones'    => [
            'listar'     => ['GET'],
            'crear'      => ['GET'],
            'guardar'    => ['POST'],
            'editar'     => ['GET'],
            'actualizar' => ['POST'],
            'eliminar'   => ['POST'],
            'activar'    => ['POST'],
        ],
    ],
];
