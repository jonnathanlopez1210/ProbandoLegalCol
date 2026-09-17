<?php
require 'app/Models/Conexion.php';
$pdo = Conexion::obtener();

// List tables
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tablas existentes: " . implode(', ', $tables) . PHP_EOL;

// Check usuarios table structure
$stmt = $pdo->describeTable('usuarios');
echo "Columnas usuarios: " . implode(', ', array_keys($stmt->getColumns())) . PHP_EOL;

// Check roles table structure
$stmt = $pdo->describeTable('roles');
echo "Columnas roles: " . implode(', ', array_keys($stmt->getColumns())) . PHP_EOL;

// Verify admin user exists
$stmt = $pdo->prepare('SELECT id, correo, rol_id, estado FROM usuarios WHERE correo = :correo');
$stmt->execute(['correo' => 'admin@sgdj.local']);
$admin = $stmt->fetch();
echo "Admin exists: " . ($admin ? 'YES' : 'NO') . PHP_EOL;
echo "Admin ID: " . ($admin['id'] ?? 'N/A') . PHP_EOL;
echo "Admin estado: " . ($admin['estado'] ?? 'N/A') . PHP_EOL;
echo "Admin rol_id: " . ($admin['rol_id'] ?? 'N/A') . PHP_EOL;
?>