<?php
$rutas = require "routes/web.php";
if (!is_array($rutas)) { die("NO-ARRAY\n"); }
$ok = true;
foreach ($rutas as $nombre => $def) {
    if (!isset($def["controlador"])) { die("SIN-CONTROLADOR:$nombre\n"); }
    if (!isset($def["acciones"]) || !is_array($def["acciones"])) { die("SIN-ACCIONES:$nombre\n"); }
    foreach ($def["acciones"] as $accion => $metodos) {
        if (!is_array($metodos) || count($metodos) === 0) { die("ACCION-VACIA:$nombre@$accion\n"); }
    }
}
echo "RUTAS-OK: " . count($rutas) . " rutas validadas\n";