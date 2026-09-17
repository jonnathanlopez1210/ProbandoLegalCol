<?php
$autoloader = function (string $clase) {
    $prefijo = 'Sgdj\\';
    if (!str_starts_with($clase, $prefijo)) { return; }
    $relativa = substr($clase, strlen($prefijo));
    $raiz = dirname(__DIR__);
    if ($relativa === 'Database/Conexion') {
        $archivo = $raiz . '/config/conexion.php';
    } else {
        $archivo = $raiz . '/' . str_replace('\\', '/', $relativa) . '.php';
    }
    echo "Clase: $clase -> Archivo: $archivo existe: " . (is_file($archivo) ? 'SÍ' : 'NO') . "\n";
};

spl_autoload_register($autoloader);
require 'config/app.php';

echo "\n--- Probando controladores ---\n";
$autoloader('Sgdj\Controllers\AuthController');
$autoloader('Sgdj\Controllers\DashboardController');
$autoloader('Sgdj\Controllers\ClientesController');
$autoloader('Sgdj\Controllers\ProcesosController');
$autoloader('Sgdj\Controllers\DocumentosController');
$autoloader('Sgdj\Controllers\BusquedaController');
$autoloader('Sgdj\Controllers\UsuariosController');
?>