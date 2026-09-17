<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 600px; margin: 2rem auto; padding: 2rem; background: #fff; }
        h1 { text-align: center; margin-bottom: 2rem; }
        .formulario { max-width: 400px; margin: 0 auto; display: flex; flex-direction: column; gap: 1rem; }
        input, select, button { padding: 0.5rem; font-size: 1rem; }
        button { cursor: pointer; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 0.5rem; font-weight: bold; }
        .form-actions { display: flex; gap: 1rem; justify-content: flex-end; }
        table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; }
        .vacío { text-align: center; padding: 2rem; color: #6b7280; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Búsqueda</h1>

        <form method="post" action="<?= url('busqueda', 'buscar'); ?>">
            <?= Csrf::campo(); ?>

            <div class="form-group">
                <label for="numero_proceso">Número de proceso</label>
                <input type="text" name="numero_proceso" id="numero_proceso"
                       value="<?= esc($numero_proceso ?? ''); ?>"
                       placeholder="Número de proceso">
            </div>

            <div class="form-group">
                <label for="nombre">Nombre del documento</label>
                <input type="text" name="nombre" id="nombre"
                       value="<?= esc($nombre ?? ''); ?>"
                       placeholder="Nombre del documento">
            </div>

            <div class="form-group">
                <label for="tipo_documento_id">Tipo de documento</label>
                <select name="tipo_documento_id" id="tipo_documento_id">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($tiposDocumento as $tipo): ?>
                        <option value="<?= esc($tipo['id']); ?>"
                            <?= ($tipo_documento_id ?? 0) == $tipo['id'] ? 'selected' : ''; ?>>
                            <?= esc($tipo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="">-- Seleccionar --</option>
                    <option value="1" <?= ($estado ?? 0) == 1 ? 'selected' : ''; ?>>
                        Activo
                    </option>
                    <option value="0" <?= ($estado ?? 0) == 0 ? 'selected' : ''; ?>>
                        Inactivo
                    </option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="buscar" value="buscar">Buscar</button>
                <button type="reset" name="limpiar" value="limpiar">Limpiar</button>
            </div>
        </form>

        <?php if (!empty($resultados)): ?>
            <div class="resultados">
                <h2>Resultados de búsqueda</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Número de proceso</th>
                            <th>Nombre</th>
                            <th>Tipo de documento</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $doc): ?>
                            <tr>
                                <td><?= esc($doc['numero_proceso'] ?? '—'); ?></td>
                                <td><?= esc($doc['nombre'] ?? '—'); ?></td>
                                <td><?= esc($doc['tipo_documento_nombre'] ?? '—'); ?></td>
                                <td><?= formato_fecha($doc['fecha_documento']); ?></td>
                                <td><?= etiqueta_estado($doc['estado']); ?></td>
                                <td>
                                    <a href="<?= url('documentos', 'descargar', ['id' => $doc['id']]); ?>">
                                        Descargar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (!empty($numero_proceso) || !empty($nombre) || ($tipo_documento_id ?? null) !== null || ($estado ?? null) !== null): ?>
            <p class="vacío">No se encontraron documentos con los criterios de búsqueda especificados.</p>
        <?php endif; ?>

        <a href="<?= url('dashboard'); ?>">Volver al dashboard</a>
    </div>
</body>
</html>