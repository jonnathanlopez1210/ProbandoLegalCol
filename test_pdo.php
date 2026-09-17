<?php
require 'app/Models/Conexion.php';
$pdo = Conexion::obtener();
echo 'Conexión OK' . PHP_EOL;
$stmt = $pdo->query('SELECT COUNT(*) as total FROM usuarios');
$row = $stmt->fetch();
echo 'Usuarios totales: ' . $row['total'] . PHP_EOL;