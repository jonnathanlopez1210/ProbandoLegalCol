<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Procesos'); ?> | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 800px; margin: 2rem auto; padding: 2rem; background: #fff; }
        h1 { text-align: center; margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; margin: 0.2rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.5rem; border: 1px solid #ddd; text-align: left; }
        th { background: #f1f3f5; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Gestión de Procesos</h1>
        <a href="<?= url('procesos', 'crear'); ?>" class="btn">Crear proceso</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($procesos)): ?>
                <tr>
                    <td colspan="6" style="text-align:center">No hay procesos registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($procesos as $proceso): ?>
                    <tr>
                        <td><?= \esc($proceso['id']); ?></td>
                        <td><?= \esc($proceso['numero_proceso']); ?></td>
                        <td><?= \esc($proceso['titulo']); ?></td>
                        <td><?= \esc($proceso['cliente_nombre'] ?? 'N/A'); ?></td>
                        <td><?= \esc($proceso['estado_proceso_nombre'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="<?= \url('procesos', 'editar'); ?>&id=<?= $proceso['id']; ?>">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div style="margin-top:2rem; text-align:center">
            <a href="<?= url('dashboard'); ?>">Volver al dashboard</a>
        </div>
    </div>
</body>
</html>