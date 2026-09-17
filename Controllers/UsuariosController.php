<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Models\Usuario;
use Sgdj\Middleware\Auth;
use Sgdj\Middleware\Csrf;
use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\flash;
use function Sgdj\helpers\esc;

/**
 * Controlador de usuarios.
 *
 * Acciones definidas en routes/web.php:
 *   - 'listar'     => ['GET']
 *   - 'crear'      => ['GET']
 *   - 'guardar'    => ['POST']
 *   - 'editar'     => ['GET']
 *   - 'actualizar' => ['POST']
 *   - 'eliminar'   => ['POST']
 *
 * Nota: la gestión de credenciales y roles se integra en el bloque 3.1.3.
 * Por ahora, estructura y validación completa.
 */
final class UsuariosController
{
    public function listar(): void
    {
        // Requerir autenticación y rol de administrador
        Auth::requireLogin();
        Auth::requireRol('Administrador');

        $usuarios = Usuario::listar();

        renderizar('usuarios/lista', [
            'usuarios' => $usuarios,
            'titulo'   => 'Gestión de Usuarios',
        ]);
    }

    public function crear(): void
    {
        // Requerir autenticación y rol de administrador
        Auth::requireLogin();
        Auth::requireRol('Administrador');

        renderizar('usuarios/crear', [
            'titulo' => 'Crear usuario',
        ]);
    }

    public function guardar(): void
    {
        // Validar método POST (el Router ya lo hará, pero es buena práctica)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            abortar_http(405, 'Método no permitido.');
        }

        // Validar CSRF
        Csrf::verificar();

        // Recibir datos del formulario
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validar campos obligatorios
        if ($nombre === '' || $email === '' || $rol === '') {
            flash('Todos los campos son obligatorios.');
            redirigir('usuarios');
        }

        // Validar y preparar datos para el modelo
        $datos = [
            'nombres'  => $nombre,
            'apellidos' => $_POST['apellidos'] ?? '',
            'correo'   => $email,
            'rol_id'   => $rol,
            'password' => $password,
        ];

        try {
            // Validar y crear usuario mediante modelo
            Usuario::crear($datos);
            flash('Usuario creado correctamente.');
        } catch (InvalidArgumentException $e) {
            flash('Error de validación: ' . esc($e->getMessage()));
        } catch (RuntimeException $e) {
            flash('Error al crear usuario: ' . esc($e->getMessage()));
        }

        redirigir('usuarios');
    }

    public function editar(): void
    {
        // Requerir autenticación y rol de administrador
        Auth::requireLogin();
        Auth::requireRol('Administrador');

        // El ID debe venir en la solicitud, pero el controlador lo obtiene por GET
        // y se pasa a la vista. Según routes/web.php, la acción 'editar' es GET.
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        // Obtener datos del usuario para prellenar el formulario
        $usuario = Usuario::obtenerPorId($id);
        if ($usuario === false) {
            abortar_http(404, 'Usuario no encontrado.');
        }

        renderizar('usuarios/editar', [
            'titulo'    => 'Editar usuario',
            'id'        => $id,
            'nombre'    => $usuario['nombres'] ?? '',
            'apellidos' => $usuario['apellidos'] ?? '',
            'correo'    => $usuario['correo'] ?? '',
            'rol_id'    => $usuario['rol_id'] ?? 1,
        ]);
    }

    public function actualizar(): void
    {
        // Validar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            abortar_http(405, 'Método no permitido.');
        }

        // Validar CSRF
        Csrf::verificar();

        // Recibir y validar ID
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        // Recibir datos del formulario
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $nueva_password = $_POST['password'] ?? '';

        // Validar campos obligatorios (siempre nombres, apellidos, correo, rol)
        if ($nombre === '' || $email === '' || $rol === '') {
            abortar_http(400, 'Datos incompletos para actualizar.');
        }

        // Obtener usuario actual para conservar password si no se cambia
        $usuarioActual = Usuario::obtenerPorId($id);
        if ($usuarioActual === false) {
            abortar_http(404, 'Usuario no encontrado.');
        }

        // Determinar el password a almacenar
        $passwordFinal = $nueva_password !== '' 
            ? password_hash($nueva_password, PASSWORD_DEFAULT) 
            : $usuarioActual['password'];

        // Preparar datos para el modelo
        $datos = [
            'nombres'  => $nombre,
            'apellidos' => $_POST['apellidos'] ?? '',
            'correo'   => $email,
            'rol_id'   => $rol,
            'password' => $passwordFinal,
        ];

        try {
            // Actualizar usuario mediante modelo
            $resultado = Usuario::actualizar($id, $datos);

            if ($resultado === false) {
                abortar_http(500, 'No se pudo actualizar el usuario. Puede que el correo ya exista.');
            }

            flash('Usuario actualizado correctamente.');
        } catch (InvalidArgumentException $e) {
            abortar_http(400, 'Error de validación: ' . esc($e->getMessage()));
        } catch (RuntimeException $e) {
            abortar_http(500, 'Error al actualizar usuario: ' . esc($e->getMessage()));
        }

        redirigir('usuarios');
    }

    public function eliminar(): void
    {
        // Validar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            abortar_http(405, 'Método no permitido.');
        }

        // Validar CSRF
        Csrf::verificar();

        // Recibir y validar ID
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        // En lugar de borrar físicamente, cambiar el estado a inactivo (0)
        $resultado = Usuario::cambiarEstado($id, 0);

        if ($resultado === false) {
            abortar_http(404, 'Usuario no encontrado.');
        }

        flash('Usuario desactivado correctamente.');
        redirigir('usuarios');
    }

    public function activar(): void
    {
        // Requerir autenticación y rol de administrador
        Auth::requireLogin();
        Auth::requireRol('Administrador');

        // Validar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            abortar_http(405, 'Método no permitido.');
        }

        // Validar CSRF
        Csrf::verificar();

        // Recibir y validar ID
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        // Activar usuario cambiando estado a 1
        $resultado = Usuario::cambiarEstado($id, 1);

        if ($resultado === false) {
            abortar_http(404, 'Usuario no encontrado.');
        }

        flash('Usuario activado correctamente.');
        redirigir('usuarios');
    }
}