<?php

declare(strict_types=1);

/** @var array $cliente */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Editar cliente'); ?> | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 600px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 1.5rem; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        label { font-weight: 500; }
        input, select, textarea { padding: 0.5rem; font-size: 1rem; border: 1px solid #ddd; border-radius: 4px; }
        button { cursor: pointer; padding: 0.75rem; background: #2563eb; color: white; border: none; border-radius: 4px; }
        button:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Editar Cliente</h1>
        
        <form action="<?= \url('clientes', 'actualizar'); ?>" method="post">
            <input type="hidden" name="id" value="<?= \esc($cliente['id']); ?>">

            <label>Tipo de Cliente:</label>
            <select name="tipo_cliente" required>
                <option value="natural" <?= $cliente['tipo_cliente'] === 'natural' ? 'selected' : ''; ?>>Persona Natural</option>
                <option value="juridica" <?= $cliente['tipo_cliente'] === 'juridica' ? 'selected' : ''; ?>>Persona Jurídica</option>
            </select>

            <label>Tipo de Identificación:</label>
            <input type="text" name="tipo_identificacion" value="<?= \esc($cliente['tipo_identificacion']); ?>" required>

            <label>Número de Identificación:</label>
            <input type="text" name="numero_identificacion" value="<?= \esc($cliente['numero_identificacion']); ?>" required>

            <label>Nombres:</label>
            <input type="text" name="nombres" value="<?= \esc($cliente['nombres'] ?? ''); ?>">

            <label>Apellidos:</label>
            <input type="text" name="apellidos" value="<?= \esc($cliente['apellidos'] ?? ''); ?>">

            <label>Razón Social:</label>
            <input type="text" name="razon_social" value="<?= \esc($cliente['razon_social'] ?? ''); ?>">

            <label>Correo Electrónico:</label>
            <input type="email" name="correo" value="<?= \esc($cliente['correo'] ?? ''); ?>">

            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?= \esc($cliente['telefono'] ?? ''); ?>">

            <label>Dirección:</label>
            <textarea name="direccion" rows="2"><?= \esc($cliente['direccion'] ?? ''); ?></textarea>

            <label>Estado:</label>
            <select name="estado">
                <option value="1" <?= (int)$cliente['estado'] === 1 ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?= (int)$cliente['estado'] === 0 ? 'selected' : ''; ?>>Inactivo</option>
            </select>

            <button type="submit">Actualizar Cliente</button>
            <a href="<?= \url('clientes'); ?>" style="text-align:center; margin-top:0.5rem; display:block;">Cancelar</a>
        </form>
    </div>
</body>
</html>
