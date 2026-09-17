<?php

declare(strict_types=1);

?>

<div class="contenedor">

    <div class="tarjeta">

        <h1>Dashboard</h1>

        <?php if (\Sgdj\Middleware\Auth::estaAutenticado()): ?>

            <?php $usuario = \Sgdj\Middleware\Auth::usuario(); ?>

            <p>
                Bienvenido,
                <?= \esc($usuario['nombres'] ?? ''); ?>
                <?= \esc($usuario['apellidos'] ?? ''); ?>
            </p>

        <?php else: ?>

            <p>Usuario no autenticado.</p>

        <?php endif; ?>

    </div>

    <div class="tarjeta">

        <h2>Módulos disponibles</h2>

        <div class="acciones">

            <a href="<?= \url('clientes', 'listar'); ?>">
                Clientes
            </a>

            <a href="<?= \url('procesos', 'listar'); ?>">
                Procesos
            </a>

            <a href="<?= \url('documentos', 'listar'); ?>">
                Documentos
            </a>

            <a href="<?= \url('busqueda', 'mostrar'); ?>">
                Búsqueda
            </a>

            <a href="<?= \url('usuarios', 'listar'); ?>">
                Usuarios
            </a>

        </div>

    </div>

</div>