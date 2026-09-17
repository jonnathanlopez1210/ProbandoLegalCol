<?php

declare(strict_types=1);

namespace Sgdj\Models;

use PDO;
use PDOException;
use RuntimeException;
use Sgdj\Database\Conexion;
use function Sgdj\helpers\esc;

/**
 * Modelo de datos para la entidad 'clientes'.
 *
 * Responsabilidades:
 *   - Listar clientes (opcionalmente filtrados por estado).
 *   - Obtener un cliente por su ID.
 *   - Crear un nuevo cliente.
 *   - Actualizar los datos de un cliente existente.
 *   - Desactivar un cliente (soft delete por medio del campo 'estado').
 *
 * Restricciones de la BD respectadas:
 *   - chk_clientes_estado: estado IN (0,1).
 *   - uq_clientes_identificacion: UNIQUE (tipo_identificacion, numero_identificacion).
 *   - tipocliente ENUM('natural','juridica').
 */
final class Cliente
{
    /**
     * Lista los clientes, opcionalmente filtrados por estado.
     *
     * @param int|null $estado 1=activo, 0=inactivo. Si es null, lista todos.
     * @return array<Array{id, tipo_cliente, tipo_identificacion, numero_identificacion, nombres, apellidos, razon_social, correo, telefono, direccion, estado}> Lista de clientes.
     * @throws RuntimeException si falla la consulta.
     */
    public static function listar(?int $estado = null): array
    {
        $pdo = Conexion::obtener();

        $sql = 'SELECT id, tipo_cliente, tipo_identificacion, numero_identificacion,
                        nombres, apellidos, razon_social, correo, telefono, direccion, estado
                FROM clientes';

        $condiciones = [];
        $parámetros = [];

        if ($estado !== null) {
            $condiciones[] = 'c.estado = :estado';
            $parámetros[':estado'] = $estado;
        }

        if (!empty($condiciones)) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }

        $sql .= ' ORDER BY nombres ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parámetros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene los datos de un cliente por su ID.
     *
     * @param int $id Identificador único del cliente.
     * @return array|false Datos del cliente o false si no existe.
     * @throws InvalidArgumentException si $no es un entero positivo.
     */
    public static function obtenerPorId(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de cliente debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        $sql = 'SELECT id, tipo_cliente, tipo_identificacion, numero_identificacion,
                        nombres, apellidos, razon_social, correo, telefono, direccion, estado
                FROM clientes
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ?: false;
    }

    /**
     * Crea un nuevo cliente en la base de datos.
     *
     * Datos obligatorios según el esquema de la BD:
     *   - tipo_cliente: 'natural' o 'juridica'
     *   - tipo_identificacion: cadena no vacía
     *   - numero_identificacion: cadena no vacía (única junto con tipo_identificacion)
     *   - nombres: cadena no vacía (para persona natural)
     *   - apellidos: cadena no vacía (para persona natural)
     *   - razon_social: cadena no vacía (para persona jurídica)
     *   - correo: cadena opcional, debe ser única
     *   - telefono: cadena opcional
     *   - direccion: cadena opcional
     *
     * @param array $datos Arreglo asociativo con los datos del cliente.
     * @return int|false ID del nuevo cliente insertado, o false en caso de error/falla de restricciones únicas.
     * @throws InvalidArgumentException si los datos obligatorios son inválidos.
     */
    public static function crear(array $datos): int|false
    {
        // Validación básica de campos obligatorios
        $tipoCliente = $datos['tipo_cliente'] ?? null;
        if (!in_array($tipoCliente, ['natural', 'juridica'], true)) {
            throw new InvalidArgumentException('El tipo de cliente debe ser "natural" o "juridica".');
        }

        $tipoIdentificacion = $datos['tipo_identificacion'] ?? '';
        $numeroIdentificacion = $datos['numero_identificacion'] ?? '';

        if ($tipoIdentificacion === '' || $numeroIdentificacion === '') {
            throw new InvalidArgumentException('El tipo y número de identificación son obligatorios.');
        }

        $nombres = $datos['nombres'] ?? '';
        $apellidos = $datos['apellidos'] ?? '';
        $razonSocial = $datos['razon_social'] ?? '';

        // Para persona natural, nombres y apellidos deben venir; para jurídica, razon_social.
        $esNatural = $tipoCliente === 'natural';
        if ($esNatural && ($nombres === '' || $apellidos === '')) {
            throw new InvalidArgumentException('Los nombres y apellidos son obligatorios para persona natural.');
        }
        if (!$esNatural && ($razonSocial === '')) {
            throw new InvalidArgumentException('La razón social es obligatoria para persona jurídica.');
        }

        $correo = $datos['correo'] ?? '';
        $telefono = $datos['telefono'] ?? '';
        $direccion = $datos['direccion'] ?? '';

        $pdo = Conexion::obtener();

        // Usar transacción para garantizar la integridad de la restricción UNIQUE
        try {
            $pdo->beginTransaction();

            $sql = 'INSERT INTO clientes (tipo_cliente, tipo_identificacion, numero_identificacion,
                            nombres, apellidos, razon_social, correo, telefono, direccion, estado)
                    VALUES (:tipo_cliente, :tipo_identificacion, :numero_identificacion,
                            :nombres, :apellidos, :razon_social, :correo, :telefono, :direccion, 1)';

            $stmt = $pdo->prepare($sql);
            $éxito = $stmt->execute([
                ':tipo_cliente'   => $tipoCliente,
                ':tipo_identificacion' => $tipoIdentificacion,
                ':numero_identificacion' => $numeroIdentificacion,
                ':nombres'        => $nombres,
                ':apellidos'      => $apellidos,
                ':razon_social'   => $razonSocial,
                ':correo'         => $correo !== '' ? $correo : null,
                ':telefono'       => $telefono !== '' ? $telefono : null,
                ':direccion'      => $direccion !== '' ? $direccion : null,
            ]);

            if (!$éxito) {
                $pdo->rollBack();
                return false;
            }

            $id = $pdo->lastInsertId();

            $pdo->commit();
            return $id;
        } catch (PDOException $e) {
            // Restricción UNIQUE violada (tipo_identificacion + numero_identificacion duplicado)
            if ($e->getCode() === '23000') {
                $pdo->rollBack();
                return false;
            }
            throw new RuntimeException('Error al crear el cliente: ' . esc($e->getMessage()));
        }
    }

    /**
     * Actualiza los datos de un cliente existente.
     *
     * Solo actualiza los campos que vienen en el arreglo $datos.
     * Los campos no proporcionados se dejan sin cambios.
     *
     * @param int $id ID del cliente a actualizar.
     * @param array $datos Arreglo asociativo con los campos a actualizar.
     * @return bool true en éxito, false en fallo.
     * @throws InvalidArgumentException si el ID no es válido.
     */
    public static function actualizar(int $id, array $datos): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de cliente debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        // Construir SET dinámico solo con campos que vienen en $datos
        $campos = [];
        $parámetros = [];

        foreach (['tipo_cliente', 'tipo_identificacion', 'numero_identificacion', 'nombres', 'apellidos',
                   'razon_social', 'correo', 'telefono', 'direccion'] as $campo) {
            if (isset($datos[$campo])) {
                $campos[] = "$campo = :$campo";
                $parámetros[":$campo"] = $datos[$campo];
            }
        }

        if (empty($campos)) {
            return false; // Nada que actualizar
        }

        $sql = 'UPDATE clientes SET ' . implode(', ', $campos) . ' WHERE id = :id';
        $parámetros[':id'] = $id;

        // Asegurar que el estado se mantenga consistente si viene en los datos
        if (isset($datos['estado'])) {
            // Validar CHECK (0 o 1)
            if (!in_array($datos['estado'], [0, 1], true)) {
                throw new InvalidArgumentException('El estado debe ser 0 o 1.');
            }
            $campos[] = 'estado = :estado';
            $parámetros[':estado'] = $datos['estado'];
        }

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute($parámetros);

        return $éxito !== false;
    }

    /**
     * Desactiva un cliente (soft set estado = 0).
     *
     * No elimina el registro físicamente, sino que lo marca como inactivo
     * respetando la restricción chk_clientes_estado de la BD.
     *
     * @param int $id ID del cliente a desactivar.
     * @return bool true en éxito, false si no existe.
     * @throws InvalidArgumentException si el ID no es válido.
     */
    public static function desactivar(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de cliente debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        $sql = 'UPDATE clientes SET estado = 0 WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute([':id' => $id]);

        // Retorna true si afectó filas (el cliente existía), false en caso contrario.
        return $stmt->rowCount() > 0;
    }
}