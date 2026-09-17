<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 400px; margin: 4rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 1.5rem; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        input, button { padding: 0.5rem; font-size: 1rem; }
        button { cursor: pointer; }
        .error { color: #dc2626; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Editar usuario</h1>
        <input type="hidden" name="id" value="<?= esc($id ?? 0); ?>">
        <form action="<?= url('usuarios', 'actualizar'); ?>" method="post">
            <?= Csrf::campo(); ?>
            <input type="text" name="nombre" placeholder="Nombre" value="<?= esc($nombre ?? ''); ?>" required>
            <input type="text" name="apellidos" placeholder="Apellidos" value="<?= esc($apellidos ?? ''); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= esc($correo ?? ''); ?>" required>
            <select name="rol" required>
                <option value="">-- Seleccionar rol --</option>
                <option value="1" <?= (isset($rolActual) && $rolActual == 1) ? 'selected' : ''; ?>>
                    Administrador
                </option>
                <option value="2" <?= (isset($rolActual) && $rolActual == 2) ? 'selected' : ''; ?>>
                    Abogado
                </option>
            </select>
            <div class="form-group">
                <label for="password">Nueva contraseña (opcional)</label>
                <input type="password" name="password" id="password" placeholder="Dejar vacío para conservar la actual">
            </div>
            <button type="submit">Actualizar</button>
            <a href="<?= url('usuarios'); ?>" style="margin-top:1rem; display:inline-block;">Cancelar</a>
        </form>
    </div>
</body>
</html>