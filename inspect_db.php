<?php
$pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=gestor_documental_juridico;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tablas en la BD:\n";
foreach ($tables as $t) echo "- $t\n";
echo "\n---\n";
foreach ($tables as $t) {
    echo "Estructura de `$t`:\n";
    $desc = $pdo->query("DESCRIBE `$t`")->fetchAll();
    foreach ($desc as $d) {
        $key = isset($d["Key"]) ? $d["Key"] : "";
        echo "  {$d[0]} ({$d[1]}) {$d[3]} {$d[2]} Key:$key\n";
    }
    echo "\n";
}
?>