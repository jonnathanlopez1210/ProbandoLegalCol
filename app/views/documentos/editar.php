<?php

declare(strict_types=1);


/** @var array $documento @var array $tiposDocumento */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Editar documento'); ?> | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 800px; margin: 2rem auto; padding: 2rem; background: #fff; }
        h1 { text-align: center; margin-bottom: 2rem; }
        .formulario { max-width: 600px; margin: 0 auto; }
        .grupo { margin-bottom: 1.5rem; }
        .grupo label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .formulario input, .formulario select { width: 100%; padding: 0.5rem; font-size: 1rem; }
        .formulario button { padding: 0.5rem 1rem; font-size: 1rem; cursor: pointer; }
        .formulario .btn-cancelar { display: inline-block; margin-top: 1rem; }
        .error { color: #dc2626; margin-bottom: 1rem; background: #fef2f2; padding: 0.5rem; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Editar documento</h1>
        <?php if ($mensajeDeError): ?>
            <div class="error"><?= esc($mensajeDeError); ?></div>
        <?php endif; ?>
        <form action="<?= url('documentos', 'actualizar'); ?>" method="post">
            <input type="hidden" name="id" value="<?= esc($documento['id']); ?>">
            <input type="hidden" name="proceso_id" value="<?= esc($documento['proceso_id']); ?>">
            <input type="hidden" name="tipo_documento_id" value="<?= esc($documento['tipo_documento_id']); ?>">
            <input type="hidden" name="usuario_id" value="<?= esc($documento['usuario_id']); ?>">
            
            <div class="grupo">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" placeholder="Nombre del documento" value="<?= esc($documento['nombre'] ?? ''); ?>" required>
            </div>
            
            <div class="grupo">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" rows="3" placeholder="Descripción del documento"><?= esc($documento['descripcion'] ?? ''); ?></textarea>
            </div>
            
            <div class="grupo">
                <label for="fecha_documento">Fecha</label>
                <input type="date" name="fecha_documento" value="<?= esc($documento['fecha_documento'] ?? ''); ?>" required>
            </div>
            
            <div class="grupo">
                <label for="estado">Estado</label>
                <select name="estado">
                    <option value="1" <?= $documento['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?= $documento['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            
            <div class="grupo">
                <button type="submit">Actualizar metadatos</button>
                <a href="<?= url('documentos'); ?>" class="btn-cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>