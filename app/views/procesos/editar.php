<?php

declare(strict_types=1);

/** @var array $proceso @var array $clientes @var array $tiposProceso @var array $estadosProceso */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Editar proceso'); ?> | Gestor Documental</title>
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
        <h1>Editar Proceso</h1>
        
        <form action="<?= \url('procesos', 'actualizar'); ?>" method="post">
            <input type="hidden" name="id" value="<?= \esc($proceso['id']); ?>">

            <label>Cliente:</label>
            <select name="cliente_id" required>
                <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id']; ?>" <?= (int)$proceso['cliente_id'] === (int)$cliente['id'] ? 'selected' : ''; ?>>
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
                <?php foreach ($tiposProceso as $tipo): ?>
                <option value="<?= $tipo['id']; ?>" <?= (int)$proceso['tipo_proceso_id'] === (int)$tipo['id'] ? 'selected' : ''; ?>>
                    <?= \esc($tipo['nombre']); ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Estado:</label>
            <select name="estado_proceso_id" required>
                <?php foreach ($estadosProceso as $estado): ?>
                <option value="<?= $estado['id']; ?>" <?= (int)$proceso['estado_proceso_id'] === (int)$estado['id'] ? 'selected' : ''; ?>>
                    <?= \esc($estado['nombre']); ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Responsable (ID Usuario):</label>
            <input type="number" name="responsable_id" value="<?= \esc($proceso['responsable_id']); ?>" min="1" required>

            <label>Número de Proceso:</label>
            <input type="text" name="numero_proceso" value="<?= \esc($proceso['numero_proceso']); ?>" required>

            <label>Título:</label>
            <input type="text" name="titulo" value="<?= \esc($proceso['titulo']); ?>" required>

            <label>Descripción:</label>
            <textarea name="descripcion" rows="3"><?= \esc($proceso['descripcion'] ?? ''); ?></textarea>

            <label>Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" value="<?= \esc($proceso['fecha_inicio'] ?? ''); ?>">

            <label>Fecha de Finalización:</label>
            <input type="date" name="fecha_finalizacion" value="<?= \esc($proceso['fecha_finalizacion'] ?? ''); ?>">

            <button type="submit">Actualizar Proceso</button>
            <a href="<?= \url('procesos'); ?>" style="text-align:center; margin-top:0.5rem; display:block;">Cancelar</a>
        </form>
    </div>
</body>
</html>
