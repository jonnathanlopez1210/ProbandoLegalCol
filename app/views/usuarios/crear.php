<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear usuario | Gestor Documental</title>
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
        <h1>Crear usuario</h1>
        <?php if ($mensajeDeError): ?>
            <div class="error"><?= esc($mensajeDeError); ?></div>
        <?php endif; ?>
        <form action="<?= url('usuarios', 'guardar'); ?>" method="post">
            <?= Csrf::campo(); ?>
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellidos" placeholder="Apellidos" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="rol" required>
                <option value="">-- Seleccionar rol --</option>
                <option value="1" <?= (isset($rolSeleccionado) && $rolSeleccionado == 1) ? 'selected' : ''; ?>>
                    Administrador
                </option>
                <option value="2" <?= (isset($rolSeleccionado) && $rolSeleccionado == 2) ? 'selected' : ''; ?>>
                    Abogado
                </option>
            </select>
            <input type="password" name="password" placeholder="Contraseña (obligatoria)" required>
            <button type="submit">Guardar</button>
            <a href="<?= url('usuarios'); ?>" style="margin-top:1rem; display:inline-block;">Cancelar</a>
        </form>
    </div>
</body>
</html>