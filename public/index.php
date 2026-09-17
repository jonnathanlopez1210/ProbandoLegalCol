<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

/**
 * Punto de entrada único de la aplicación (front controller).
 *
 * Todo el tráfico HTTP llega aquí (public/ como document root).
 * Carga la configuración general, el autoloader, el entorno y la sesión,
 * y delega el despacho del front controller al núcleo de la aplicación
 * (Sgdj\Core\Bootstrap) contra la tabla de rutas de routes/web.php.
 *
 * Flujo: public/index.php → require controladores → Bootstrap::iniciar() → Router::despachar()
 *       → Controller (Sgdj\Controllers\*) → View → Response HTTP.
 *
 * Los controladores se cargan explícitamente aquí para asegurar que estén
 * disponibles cuando el Router invoque `new $clase()` en despachar().
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/Core/Bootstrap.php';



// Cargar controladores explícitamente (el Router los instanciará vía new $clase())
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ClientesController.php';
require_once __DIR__ . '/../app/controllers/ProcesosController.php';
require_once __DIR__ . '/../app/controllers/DocumentosController.php';
require_once __DIR__ . '/../app/controllers/BusquedaController.php';
require_once __DIR__ . '/../app/controllers/UsuariosController.php';

use Sgdj\Core\Bootstrap;

Bootstrap::iniciar();

?>