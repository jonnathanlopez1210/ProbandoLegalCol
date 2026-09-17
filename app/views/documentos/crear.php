<?php

declare(strict_types=1);


/** @var string $titulo @var array $tiposDocumento @var array $procesos */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \esc($titulo ?? 'Crear documento'); ?> | Gestor Documental</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .contenedor { max-width: 800px; margin: 2rem auto; padding: 2rem; background: #fff; }
        h1 { text-align: center; margin-bottom: 2rem; }
        .formulario { max-width: 600px; margin: 0 auto; }
        .grupo { margin-bottom: 1.5rem; }
        .grupo label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .formulario input, .formulario select, .formulario textarea { width: 100%; padding: 0.5rem; font-size: 1rem; }
        .formulario input[type="file"] { padding: 0; }
        .formulario button { padding: 0.5rem 1rem; font-size: 1rem; cursor: pointer; }
        .formulario .btn-cancelar { display: inline-block; margin-top: 1rem; }
        .error { color: #dc2626; margin-bottom: 1rem; background: #fef2f2; padding: 0.5rem; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Crear documento</h1>
        
        <form action="<?= \url('documentos', 'guardar'); ?>" method="post" enctype="multipart/form-data" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= \esc(\Sgdj\Middleware\Csrf::token()); ?>">
            
            <div class="grupo">
                <label for="proceso_id">Proceso</label>
                <select name="proceso_id" id="proceso_id" required>
                    <option value="">-- Seleccione un proceso --</option>
                    <?php foreach ($procesos as $proceso): ?>
                        <option value="<?= \esc($proceso['id']); ?>"><?= \esc($proceso['numero_proceso'] . ' - ' . $proceso['titulo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="grupo">
                <label for="tipo_documento_id">Tipo de documento</label>
                <select name="tipo_documento_id" id="tipo_documento_id" required>
                    <option value="">-- Seleccione un tipo --</option>
                    <?php foreach ($tiposDocumento as $tipo): ?>
                        <option value="<?= \esc($tipo['id']); ?>"><?= \esc($tipo['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="grupo">
                <label for="nombre">Nombre/Título</label>
                <input type="text" name="nombre" placeholder="Nombre del documento" required>
            </div>
            
            <div class="grupo">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" rows="3" placeholder="Descripción del documento"></textarea>
            </div>
            
            <div class="grupo">
                <label for="fecha_documento">Fecha</label>
                <input type="date" name="fecha_documento" required>
            </div>
            
            <div class="grupo">
                <label for="archivo">Archivo</label>
                <input type="file" name="archivo" id="archivo" accept=".pdf,.doc,.docx,.xls,.xlsx">
            </div>
            
            <div class="grupo">
                <button type="submit">Guardar documento</button>
                <a href="<?= \url('documentos'); ?>" class="btn-cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>