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

// Test with existing middleware
echo "Probando Sgdj\Middleware\Auth:\n";
$autoloader('Sgdj\Middleware\Auth');

echo "\nProbando Sgdj\Core\Bootstrap:\n";
$autoloader('Sgdj\Core\Bootstrap');

echo "\nProbando Sgdj\Controllers\AuthController (nuevo):\n";
$autoloader('Sgdj\Controllers\AuthController');
?>