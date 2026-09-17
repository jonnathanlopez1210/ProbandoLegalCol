<?php

declare(strict_types=1);

/**
 * Funciones auxiliares globales de la aplicación.
 * Este archivo se carga desde public/index.php antes del arranque.
 */

use Sgdj\Middleware\Auth;

/**
 * Escapa texto para una salida segura en HTML (protección contra XSS).
 */
function esc(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

/**
 * Normaliza una entrada de texto quitando espacios innecesarios.
 */
function normalizar(?string $valor): string
{
    return trim((string) $valor);
}

/**
 * Construye una URL interna relativa del tipo ?ruta=...&accion=...
 */
function url(string $ruta, ?string $accion = null, array $params = []): string
{
    if ($accion !== null) {
        $params['accion'] = $accion;
    }

    $consulta = ['ruta' => $ruta] + $params;

    return '?' . http_build_query($consulta);
}

/**
 * Redirige a una ruta interna y detiene la ejecución.
 */
function redirigir(string $ruta, ?string $accion = null): never
{
    $destino = BASE_URL . '/index.php' . url($ruta, $accion);

    header('Location: ' . $destino);
    exit;
}

/**
 * Lee un valor de entrada (POST preferentemente, luego GET) normalizado.
 */
function campo(string $clave, string $default = ''): string
{
    return normalizar((string) ($_POST[$clave] ?? $_GET[$clave] ?? $default));
}

/**
 * Almacena un mensaje flash en la sesión y lo recupera una sola vez.
 * Uso: flash('error', 'Mensaje') para guardar; flash('error') para leer y borrar.
 */
function flash(string $tipo, ?string $mensaje = null): ?string
{
    if ($mensaje === null) {
        $valor = $_SESSION['flash'][$tipo] ?? null;
        unset($_SESSION['flash'][$tipo]);
        return $valor;
    }

    $_SESSION['flash'][$tipo] = $mensaje;
    return null;
}

/**
 * Devuelve los datos del usuario autenticado o null si no hay sesión.
 */
function usuario_sesion(): ?array
{
    return Auth::usuario();
}

/**
 * Indica si existe una sesión autenticada.
 */
function esta_autenticado(): bool
{
    return Auth::estaAutenticado();
}

/**
 * Texto legible para estados lógicos (1 = activo, 0 = inactivo).
 */
function etiqueta_estado($estado): string
{
    return (int) $estado === 1 ? 'Activo' : 'Inactivo';
}

/**
 * Renderiza una vista dentro del layout correspondiente.
 * - auth/* y errores/* usan un layout independiente.
 * - El resto usa el layout administrativo (header + sidebar + contenido).
 */
function renderizar(string $vista, array $datos = []): void
{
    $archivo = ROOT_PATH . '/app/views/' . $vista . '.php';

    if (!is_file($archivo)) {
        http_response_code(500);
        exit('Vista no encontrada: ' . $vista);
    }

    extract($datos, EXTR_SKIP);

    ob_start();
    include $archivo;
    $contenido = ob_get_clean();

    $layout = 'layout/principal';

    if (str_starts_with($vista, 'auth/') || str_starts_with($vista, 'errores/')) {
        $layout = 'layout/independiente';
    }

    include ROOT_PATH . '/app/views/' . $layout . '.php';
}

/**
 * Presenta una respuesta HTTP de error con una vista y detiene la ejecución.
 */
function abortar_http(int $codigo, string $mensaje = 'Ha ocurrido un error.'): never
{
    http_response_code($codigo);
    renderizar('errores/error', ['codigo' => $codigo, 'mensaje' => $mensaje]);
    exit;
}

/**
 * Formatea una fecha DATETIME o timestamp a texto (dd/mm/YYYY).
 */
function formato_fecha(?string $fecha): string
{
    if ($fecha === null || $fecha === '') {
        return '—';
    }

    $ts = strtotime($fecha);
    return $ts === false ? '—' : date('d/m/Y', $ts);
}