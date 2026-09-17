<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Crear cliente'); ?> | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 600px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 1.5rem; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        label { font-weight: 500; }
        input, select, textarea { padding: 0.5rem; font-size: 1rem; border: 1px solid #ddd; border-radius: 4px; }
        button { cursor: pointer; padding: 0.75rem; background: #2563eb; color: white; border: none; border-radius: 4px; }
        button:hover { background: #1d4ed8; }
        .error { color: #dc2626; margin-bottom: 1rem; padding: 0.5rem; background: #fee; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Crear Cliente</h1>
        
        <form action="<?= \url('clientes', 'guardar'); ?>" method="post">
            <label>Tipo de Cliente:</label>
            <select name="tipo_cliente" required>
                <option value="">-- Seleccione --</option>
                <option value="natural">Persona Natural</option>
                <option value="juridica">Persona Jurídica</option>
            </select>

            <label>Tipo de Identificación:</label>
            <input type="text" name="tipo_identificacion" placeholder="CC, NIT, CE, etc." required>

            <label>Número de Identificación:</label>
            <input type="text" name="numero_identificacion" placeholder="Número único" required>

            <label>Nombres:</label>
            <input type="text" name="nombres" placeholder="Nombres (persona natural)">

            <label>Apellidos:</label>
            <input type="text" name="apellidos" placeholder="Apellidos (persona natural)">

            <label>Razón Social:</label>
            <input type="text" name="razon_social" placeholder="Razón social (persona jurídica)">

            <label>Correo Electrónico:</label>
            <input type="email" name="correo" placeholder="correo@ejemplo.com">

            <label>Teléfono:</label>
            <input type="text" name="telefono" placeholder="Teléfono de contacto">

            <label>Dirección:</label>
            <textarea name="direccion" rows="2" placeholder="Dirección completa"></textarea>

            <button type="submit">Guardar Cliente</button>
            <a href="<?= \url('clientes'); ?>" style="text-align:center; margin-top:0.5rem; display:block;">Cancelar</a>
        </form>
    </div>
</body>
</html>
