<?php

declare(strict_types=1);

/** @var string|null $titulo */
/** @var int|null $codigo */
/** @var string|null $mensaje */
/** @var string|null $contenido */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= \esc($titulo ?? 'Acceso'); ?></title>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/css/styles.css"
    >
</head>
<body>
    <main class="contenido">
        <div class="contenedor">

            <div class="tarjeta">
                <h2><?= \esc($titulo ?? 'Legal Col'); ?></h2>

                <?php if (isset($codigo, $mensaje)): ?>
                    <div class="error">
                        <?= \esc($mensaje); ?>
                        (Código: <?= \esc((string) $codigo); ?>)
                    </div>
                <?php endif; ?>

                <?php $mensajeFlash = \flash('mensaje'); ?>

                <?php if ($mensajeFlash !== null): ?>
                    <div class="mensaje">
                        <?= \esc($mensajeFlash); ?>
                    </div>
                <?php endif; ?>

                <?= $contenido ?? ''; ?>

            </div>

        </div>
    </main>
</body>
</html>
