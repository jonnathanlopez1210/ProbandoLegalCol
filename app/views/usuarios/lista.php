<?php

declare(strict_types=1);

/** @var array $usuarios */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Usuarios'); ?> | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 900px; margin: 2rem auto; padding: 2rem; background: #fff; }
        h1 { text-align: center; margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; margin: 0.2rem; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.5rem; border: 1px solid #ddd; text-align: left; }
        th { background: #f1f3f5; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Gestión de Usuarios</h1>
        <a href="<?= \url('usuarios', 'crear'); ?>" class="btn">Crear Usuario</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="6" style="text-align:center">No hay usuarios registrados</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= \esc($usuario['id']); ?></td>
                        <td><?= \esc($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></td>
                        <td><?= \esc($usuario['correo']); ?></td>
                        <td><?= \esc($usuario['rol_nombre'] ?? 'N/A'); ?></td>
                        <td><?= (int)$usuario['estado'] === 1 ? 'Activo' : 'Inactivo'; ?></td>
                        <td>
                            <a href="<?= \url('usuarios', 'editar'); ?>&id=<?= $usuario['id']; ?>" class="btn-sm">Editar</a>
                            <?php if ((int)$usuario['estado'] === 1): ?>
                            <form action="<?= \url('usuarios', 'eliminar'); ?>" method="post" style="display:inline">
                                <input type="hidden" name="id" value="<?= $usuario['id']; ?>">
                                <button type="submit" class="btn-sm" onclick="return confirm('¿Desactivar este usuario?')">Desactivar</button>
                            </form>
                            <?php else: ?>
                            <form action="<?= \url('usuarios', 'activar'); ?>" method="post" style="display:inline">
                                <input type="hidden" name="id" value="<?= $usuario['id']; ?>">
                                <button type="submit" class="btn-sm">Activar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div style="margin-top:2rem; text-align:center">
            <a href="<?= \url('dashboard'); ?>">Volver al dashboard</a>
        </div>
    </div>
</body>
</html>
