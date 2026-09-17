<?php

declare(strict_types=1);


/** @var int $codigo @var string $mensaje */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= esc($codigo); ?></title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 500px; margin: 4rem auto; padding: 2rem; text-align: center; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .codigo { font-size: 6rem; font-weight: bold; color: #fca5a5; }
        .mensaje { color: #6b7280; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="codigo"><?= esc($codigo); ?></div>
        <div class="mensaje"><?= esc($mensaje); ?></div>
        <p><a href="<?= url('login'); ?>">Volver al login</a></p>
    </div>
</body>
</html>