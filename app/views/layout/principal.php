<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= \esc($titulo ?? 'Gestor Documental Jurídico'); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>/css/styles.css"
    >
</head>

<body>

    <header class="encabezado">
        <div class="contenedor">

            <h1>Gestor Documental Jurídico</h1>

            <nav>
                <a href="<?= \url('dashboard', 'mostrar'); ?>">
                    Dashboard
                </a>

                <form
                    action="<?= \url('salir', 'cerrar'); ?>"
                    method="post"
                    style="display: inline;"
                >
                    <?= \Sgdj\Middleware\Csrf::campo(); ?>

                    <button type="submit" class="btn">
                        Cerrar sesión
                    </button>
                </form>
            </nav>

        </div>
    </header>

    <main class="contenido">

        <div class="contenedor">

            <?php $mensajeError = \flash('error'); ?>

            <?php if ($mensajeError !== null): ?>
                <div class="error">
                    <?= \esc($mensajeError); ?>
                </div>
            <?php endif; ?>

            <?php $mensajeExito = \flash('exito'); ?>

            <?php if ($mensajeExito !== null): ?>
                <div class="mensaje">
                    <?= \esc($mensajeExito); ?>
                </div>
            <?php endif; ?>

            <?= $contenido ?? ''; ?>

        </div>

    </main>

    <footer class="pie">

        <div class="contenedor">
            <p>
                Gestor Documental Jurídico - Fase 3
            </p>
        </div>

    </footer>

</body>
</html>