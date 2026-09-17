<?php

declare(strict_types=1);

/** @var string|null $titulo */
/** @var string|null $mensajeDeError */
?>

<h2>Iniciar sesión</h2>

<?php if (isset($mensajeDeError) && $mensajeDeError !== null && $mensajeDeError !== ''): ?>
    <div class="error">
        <?= \esc($mensajeDeError); ?>
    </div>
<?php endif; ?>

<form
    class="formulario"
    action="<?= \url('login', 'iniciar'); ?>"
    method="post"
>
    <input
        type="text"
        name="usuario"
        placeholder="Usuario"
        required
    >

    <input
        type="password"
        name="contrasena"
        placeholder="Contraseña"
        required
    >

    <input
        type="hidden"
        name="csrf_token"
        value="<?= \esc(\Sgdj\Middleware\Csrf::token()); ?>"
    >

    <button type="submit">Entrar</button>
</form>