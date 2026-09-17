<?php

declare(strict_types=1);

/** @var array $clientes @var array $tiposProceso @var array $estadosProceso */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Crear proceso'); ?> | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 700px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
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
        <h1>Crear Proceso</h1>
        
        <form action="<?= \url('procesos', 'guardar'); ?>" method="post">
            <label>Cliente:</label>
            <select name="cliente_id" required>
                <option value="">-- Seleccione un cliente --</option>
                <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id']; ?>">
                    <?php if ($cliente['tipo_cliente'] === 'natural'): ?>
                        <?= \esc($cliente['nombres'] . ' ' . $cliente['apellidos']); ?>
                    <?php else: ?>
                        <?= \esc($cliente['razon_social']); ?>
                    <?php endif; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Tipo de Proceso:</label>
            <select name="tipo_proceso_id" required>
                <option value="">-- Seleccione tipo --</option>
                <?php foreach ($tiposProceso as $tipo): ?>
                <option value="<?= $tipo['id']; ?>"><?= \esc($tipo['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <label>Estado:</label>
            <select name="estado_proceso_id" required>
                <option value="">-- Seleccione estado --</option>
                <?php foreach ($estadosProceso as $estado): ?>
                <option value="<?= $estado['id']; ?>"><?= \esc($estado['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <label>Responsable (ID Usuario):</label>
            <input type="number" name="responsable_id" min="1" required>

            <label>Número de Proceso:</label>
            <input type="text" name="numero_proceso" placeholder="Ej: 2024-001" required>

            <label>Título:</label>
            <input type="text" name="titulo" placeholder="Título del proceso" required>

            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" placeholder="Descripción detallada"></textarea>

            <label>Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio">

            <label>Fecha de Finalización:</label>
            <input type="date" name="fecha_finalizacion">

            <button type="submit">Crear Proceso</button>
            <a href="<?= \url('procesos'); ?>" style="text-align:center; margin-top:0.5rem; display:block;">Cancelar</a>
        </form>
    </div>
</body>
</html>
