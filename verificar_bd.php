<?php
require 'app/Models/Conexion.php';
$pdo = Conexion::obtener();
$stmt = $pdo->query("SELECT id, correo, rol_id, estado FROM usuarios WHERE id = 2");
$row = $stmt->fetch();
echo "ID: " . $row["id"] . PHP_EOL;
echo "Correo: " . $row["correo"] . PHP_EOL;
echo "Rol ID: " . $row["rol_id"] . PHP_EOL;
echo "Estado: " . $row["estado"] . PHP_EOL;
?>