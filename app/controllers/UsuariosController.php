<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Models\Usuario;
use Sgdj\Repositories\RolRepository;
use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\esc;
use function Sgdj\helpers\flash;

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
 * Nota: la gestión de credenciales y roles se integra en el bloque
 * 3.1.3. Por ahora, estructura y validación básica.
 */
final class UsuariosController
{
    public function listar(): void
    {
        $usuarios = Usuario::listar();
        renderizar('usuarios/lista', ['usuarios' => $usuarios]);
    }

    public function crear(): void
    {
        $pdo = \Sgdj\Database\Conexion::obtener();
        $rolRepo = new RolRepository($pdo);
        $roles = $rolRepo->listarActivos();
        
        renderizar('usuarios/crear', ['roles' => $roles]);
    }

    public function guardar(): void
    {
        $nombre = $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $email = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';
        $rol = (int)($_POST['rol_id'] ?? 0);

        if ($nombre === '' || $apellidos === '' || $email === '' || $password === '' || $rol === 0) {
            flash('error', 'Todos los campos son obligatorios.');
            redirigir('usuarios');
        }

        try {
            Usuario::crear([
                'rol_id' => $rol,
                'nombres' => $nombre,
                'apellidos' => $apellidos,
                'correo' => $email,
                'password' => $password,
                'estado' => 1
            ]);
            flash('exito', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            flash('error', 'Error al crear usuario: ' . esc($e->getMessage()));
        }
        
        redirigir('usuarios');
    }

    public function editar(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        $usuario = Usuario::obtenerPorId($id);
        if ($usuario === false) {
            abortar_http(404, 'Usuario no encontrado.');
        }
        
        $pdo = \Sgdj\Database\Conexion::obtener();
        $rolRepo = new RolRepository($pdo);
        $roles = $rolRepo->listarActivos();

        renderizar('usuarios/editar', ['usuario' => $usuario, 'roles' => $roles]);
    }

    public function actualizar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        $datos = [
            'nombres' => $_POST['nombres'] ?? '',
            'apellidos' => $_POST['apellidos'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'rol_id' => (int)($_POST['rol_id'] ?? 0),
            'estado' => (int)($_POST['estado'] ?? 1)
        ];
        
        // Solo actualizar contraseña si se proporciona
        if (!empty($_POST['password'])) {
            $datos['password'] = $_POST['password'];
        }

        try {
            Usuario::actualizar($id, $datos);
            flash('exito', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            flash('error', 'Error al actualizar usuario: ' . esc($e->getMessage()));
        }
        
        redirigir('usuarios');
    }

    public function eliminar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        try {
            Usuario::desactivar($id);
            flash('exito', 'Usuario desactivado correctamente.');
        } catch (\Exception $e) {
            flash('error', 'Error al desactivar usuario: ' . esc($e->getMessage()));
        }
        
        redirigir('usuarios');
    }

    public function activar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de usuario no válido.');
        }

        try {
            Usuario::activar($id);
            flash('exito', 'Usuario activado correctamente.');
        } catch (\Exception $e) {
            flash('error', 'Error al activar usuario: ' . esc($e->getMessage()));
        }
        
        redirigir('usuarios');
    }
}