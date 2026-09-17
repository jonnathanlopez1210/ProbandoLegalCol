<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Clientes'); ?> | Gestor Documental</title>
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
        <h1>Gestión de Clientes</h1>
        <a href="<?= url('clientes', 'crear'); ?>" class="btn">Crear cliente</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                <tr>
                    <td colspan="4" style="text-align:center">No hay clientes registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= \esc($cliente['id']); ?></td>
                        <td>
                            <?php if ($cliente['tipo_cliente'] === 'natural'): ?>
                                <?= \esc($cliente['nombres'] . ' ' . $cliente['apellidos']); ?>
                            <?php else: ?>
                                <?= \esc($cliente['razon_social']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?= \esc($cliente['correo'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="<?= \url('clientes', 'editar'); ?>&id=<?= $cliente['id']; ?>">Editar</a>
                            <?php if ((int)$cliente['estado'] === 1): ?>
                            <form action="<?= \url('clientes', 'eliminar'); ?>" method="post" style="display:inline">
                                <input type="hidden" name="id" value="<?= $cliente['id']; ?>">
                                <button type="submit" onclick="return confirm('¿Desactivar este cliente?')">Desactivar</button>
                            </form>
                            <?php endif; ?>
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