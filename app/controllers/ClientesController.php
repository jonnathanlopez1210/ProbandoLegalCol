<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Models\Cliente;
use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\esc;
use function Sgdj\helpers\flash;

/**
 * Controlador de clientes.
 *
 * Acciones definidas en routes/web.php:
 *   - 'listar'     => ['GET']
 *   - 'crear'      => ['GET']
 *   - 'guardar'    => ['POST']
 *   - 'editar'     => ['GET']
 *   - 'actualizar' => ['POST']
 *   - 'eliminar'   => ['POST']
 *
 * El controlador usa el modelo `Cliente` para operar contra la base de datos.
 */
final class ClientesController
{
    public function listar(): void
    {
        $clientes = Cliente::listar(); // trae todos (o filtrar por estado después)
        renderizar('clientes/lista', ['clientes' => $clientes]);
    }

    public function crear(): void
    {
        renderizar('clientes/crear', []);
    }

    public function guardar(): void
    {
        $resultado = Cliente::crear([
            'tipo_cliente'        => $_POST['tipo_cliente'] ?? '',
            'tipo_identificacion' => $_POST['tipo_identificacion'] ?? '',
            'numero_identificacion' => $_POST['numero_identificacion'] ?? '',
            'nombres'             => $_POST['nombres'] ?? '',
            'apellidos'           => $_POST['apellidos'] ?? '',
            'razon_social'        => $_POST['razon_social'] ?? '',
            'correo'              => $_POST['correo'] ?? '',
            'telefono'            => $_POST['telefono'] ?? '',
            'direccion'           => $_POST['direccion'] ?? '',
        ]);

        if ($resultado === false) {
            // Restricción UNIQUE violada (tipo_identificacion + numero_identificacion duplicado)
            flash('error', 'No es posible guardar el cliente: ya existe otro con el mismo tipo y número de identificación.');
            redirigir('clientes');
        }

        flash('exito', 'Cliente guardado correctamente.');
        redirigir('clientes');
    }

    public function editar(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de cliente no válido.');
        }

        $cliente = Cliente::obtenerPorId($id);

        if ($cliente === false) {
            abortar_http(404, 'El cliente no existe.');
        }

        renderizar('clientes/editar', ['cliente' => $cliente]);
    }

    public function actualizar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de cliente no válido.');
        }

        $resultado = Cliente::actualizar($id, [
            'tipo_cliente'        => $_POST['tipo_cliente'] ?? '',
            'tipo_identificacion' => $_POST['tipo_identificacion'] ?? '',
            'numero_identificacion' => $_POST['numero_identificacion'] ?? '',
            'nombres'             => $_POST['nombres'] ?? '',
            'apellidos'           => $_POST['apellidos'] ?? '',
            'razon_social'        => $_POST['razon_social'] ?? '',
            'correo'              => $_POST['correo'] ?? '',
            'telefono'            => $_POST['telefono'] ?? '',
            'direccion'           => $_POST['direccion'] ?? '',
            'estado'              => (int)($_POST['estado'] ?? 1),
        ]);

        if ($resultado === false) {
            flash('error', 'No fue posible actualizar el cliente.');
            redirigir('clientes');
        }

        flash('exito', 'Cliente actualizado correctamente.');
        redirigir('clientes');
    }

    public function eliminar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de cliente no válido.');
        }

        $resultado = Cliente::desactivar($id);

        if ($resultado === false) {
            flash('error', 'No fue posible desactivar el cliente.');
            redirigir('clientes');
        }

        flash('exito', 'Cliente desactivado correctamente.');
        redirigir('clientes');
    }
}