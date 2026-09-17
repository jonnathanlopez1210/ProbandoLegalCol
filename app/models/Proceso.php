<?php

declare(strict_types=1);

namespace Sgdj\Models;

use PDO;
use PDOException;
use RuntimeException;
use InvalidArgumentException;
use Sgdj\Database\Conexion;
use function Sgdj\helpers\esc;

/**
 * Modelo de datos para la entidad 'procesos'.
 *
 * Responsabilidades:
 *   - Listar procesos con filtros opcionales (cliente, tipo, estado).
 *   - Obtener un proceso por su ID.
 *   - Crear un nuevo proceso.
 *   - Actualizar los datos de un proceso existente.
 *   - Cambiar el estado de un proceso.
 *
 * Restricciones de la BD respectadas:
 *   - uq_procesos_numero_proceso: UNIQUE (numero_proceso).
 *   - chk_procesos_fechas: fecha_finalizacion IS NULL OR >= fecha_inicio.
 *   - FK a clientes, tipos_proceso, estados_proceso, usuarios.
 */
final class Proceso
{
    /**
     * Lista los procesos, opcionalmente filtrados por cliente, tipo o estado.
     *
     * @param int|null $clienteId   Filtrar por cliente_id.
     * @param int|null $tipoProcesoId Filtrar por tipo_proceso_id.
     * @param int|null $estadoProcesoId Filtrar por estado_proceso_id.
     * @return array<Array{id, cliente_id, tipo_proceso_id, estado_proceso_id, responsable_id, numero_proceso, titulo, descripcion, fecha_inicio, fecha_finalizacion, created_at, updated_at}> Lista de procesos.
     * @throws RuntimeException si falla la consulta.
     */
    public static function listar(?int $clienteId = null, ?int $tipoProcesoId = null, ?int $estadoProcesoId = null): array
    {
        $pdo = Conexion::obtener();

        $sql = 'SELECT p.id, p.cliente_id, p.tipo_proceso_id, p.estado_proceso_id,
                        p.responsable_id, p.numero_proceso, p.titulo, p.descripcion,
                        p.fecha_inicio, p.fecha_finalizacion,
                        p.created_at, p.updated_at,
                        c.nombres AS cliente_nombre,
                        tp.nombre AS tipo_proceso_nombre,
                        ep.nombre AS estado_proceso_nombre
                FROM procesos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN tipos_proceso tp ON p.tipo_proceso_id = tp.id
                LEFT JOIN estados_proceso ep ON p.estado_proceso_id = ep.id';

        $condiciones = [];
        $parámetros = [];

        if ($clienteId !== null) {
            $condiciones[] = 'p.cliente_id = :cliente_id';
            $parámetros[':cliente_id'] = $clienteId;
        }
        if ($tipoProcesoId !== null) {
            $condiciones[] = 'p.tipo_proceso_id = :tipo_proceso_id';
            $parámetros[':tipo_proceso_id'] = $tipoProcesoId;
        }
        if ($estadoProcesoId !== null) {
            $condiciones[] = 'p.estado_proceso_id = :estado_proceso_id';
            $parámetros[':estado_proceso_id'] = $estadoProcesoId;
        }

        if (!empty($condiciones)) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }

        $sql .= ' ORDER BY p.fecha_inicio DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parámetros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene los datos de un proceso por su ID.
     *
     * @param int $id Identificador único del proceso.
     * @return array|false Datos del proceso o false si no existe.
     * @throws InvalidArgumentException si $no es un entero positivo.
     */
    public static function obtenerPorId(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de proceso debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        $sql = 'SELECT p.id, p.cliente_id, p.tipo_proceso_id, p.estado_proceso_id,
                        p.responsable_id, p.numero_proceso, p.titulo, p.descripcion,
                        p.fecha_inicio, p.fecha_finalizacion,
                        p.created_at, p.updated_at,
                        c.nombres AS cliente_nombre,
                        tp.nombre AS tipo_proceso_nombre,
                        ep.nombre AS estado_proceso_nombre
                FROM procesos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN tipos_proceso tp ON p.tipo_proceso_id = tp.id
                LEFT JOIN estados_proceso ep ON p.estado_proceso_id = ep.id
                WHERE p.id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ?: false;
    }

    /**
     * Crea un nuevo proceso en la base de datos.
     *
     * Datos obligatorios según el esquema de la BD:
     *   - cliente_id: ID de cliente existente.
     *   - tipo_proceso_id: ID de tipo de proceso existente.
     *   - estado_proceso_id: ID de estado de proceso existente.
     *   - responsable_id: ID de usuario (responsable) existente.
     *   - numero_proceso: cadena única (no repetida en la tabla).
     *   - titulo: cadena no vacía.
     *   - fecha_inicio: fecha DATE/DATETIME no vacía.
     *   - descripcion: texto opcional.
     *
     * @param array $datos Arreglo asociativo con los datos del proceso.
     * @return int|false ID del nuevo proceso insertado, o false en caso de fallo (ej. numero_proceso duplicado).
     * @throws InvalidArgumentException si los datos obligatorios son inválidos.
     */
    public static function crear(array $datos): int|false
    {
        // Validación obligatoria
        $clienteId = $datos['cliente_id'] ?? null;
        $tipoProcesoId = $datos['tipo_proceso_id'] ?? null;
        $estadoProcesoId = $datos['estado_proceso_id'] ?? null;
        $responsableId = $datos['responsable_id'] ?? null;
        $numeroProceso = $datos['numero_proceso'] ?? '';
        $titulo = $datos['titulo'] ?? '';
        $fechaInicio = $datos['fecha_inicio'] ?? '';
        $descripcion = $datos['descripcion'] ?? '';

        // Validaciones básicas
        if (!is_int($clienteId) || $clienteId <= 0) {
            throw new InvalidArgumentException('El cliente_id debe ser un entero positivo.');
        }
        if (!is_int($tipoProcesoId) || $tipoProcesoId <= 0) {
            throw new InvalidArgumentException('El tipo_proceso_id debe ser un entero positivo.');
        }
        if (!is_int($estadoProcesoId) || $estadoProcesoId <= 0) {
            throw new InvalidArgumentException('El estado_proceso_id debe ser un entero positivo.');
        }
        if (!is_int($responsableId) || $responsableId <= 0) {
            throw new InvalidArgumentException('El responsable_id debe ser un entero positivo.');
        }
        if ($numeroProceso === '') {
            throw new InvalidArgumentException('El número de proceso es obligatorio.');
        }
        if ($titulo === '') {
            throw new InvalidArgumentException('El título es obligatorio.');
        }
        if ($fechaInicio === '') {
            throw new InvalidArgumentException('La fecha de inicio es obligatoria.');
        }

        $pdo = Conexion::obtener();

        // Usar transacción para garantizar la integridad de la restricción UNIQUE en numero_proceso
        // y la restricción de CHECK de fechas.
        try {
            $pdo->beginTransaction();

            $sql = 'INSERT INTO procesos (cliente_id, tipo_proceso_id, estado_proceso_id,
                            responsable_id, numero_proceso, titulo, descripcion, fecha_inicio, fecha_finalizacion, estado)
                    VALUES (:cliente_id, :tipo_proceso_id, :estado_proceso_id,
                            :responsable_id, :numero_proceso, :titulo, :descripcion, :fecha_inicio, NULL, 1)';

            $stmt = $pdo->prepare($sql);
            $éxito = $stmt->execute([
                ':cliente_id'          => $clienteId,
                ':tipo_proceso_id'     => $tipoProcesoId,
                ':estado_proceso_id'   => $estadoProcesoId,
                ':responsable_id'      => $responsableId,
                ':numero_proceso'      => $numeroProceso,
                ':titulo'              => $titulo,
                ':descripcion'         => $descripcion,
                ':fecha_inicio'        => $fechaInicio,
            ]);

            if (!$éxito) {
                $pdo->rollBack();
                return false;
            }

            // Validar la restricción CHECK de fechas a nivel de aplicación
            // (aunque la BD también la valida, hacemos la comprobación antes de confirmar).
            $fechaFinalizacion = $datos['fecha_finalizacion'] ?? null;
            if ($fechaFinalizacion !== null && $fechaFinalizacion !== '') {
                // Parse simple: esperamos formato 'Y-m-d H:i:s' o 'Y-m-d'
                $inicioTs = strtotime($fechaInicio);
                $finTs = strtotime($fechaFinalizacion);
                if ($inicioTs !== false && $finTs !== false && $finTs < $inicioTs) {
                    $pdo->rollBack();
                    throw new InvalidArgumentException('La fecha de finalización no puede ser anterior a la fecha de inicio.');
                }
            }

            $id = $pdo->lastInsertId();

            $pdo->commit();
            return $id;
        } catch (PDOException $e) {
            // Restricción UNIQUE violada (numero_proceso duplicado)
            if ($e->getCode() === '23000') {
                $pdo->rollBack();
                return false;
            }
            // Restricción de FK violada (cliente/tipo/estado/responsable inexistente)
            if ($e->getCode() === '23000') {
                $pdo->rollBack();
                return false;
            }
            throw new RuntimeException('Error al crear el proceso: ' . esc($e->getMessage()));
        }
    }

    /**
     * Actualiza los datos de un proceso existente.
     *
     * Solo actualiza los campos que vienen en el arreglo $datos.
     *
     * @param int $id ID del proceso a actualizar.
     * @param array $datos Arreglo asociativo con los campos a actualizar.
     * @return bool true en éxito, false en fallo.
     * @throws InvalidArgumentException si el ID no es válido.
     */
    public static function actualizar(int $id, array $datos): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de proceso debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        // Construir SET dinámico solo con campos que vienen en $datos
        $campos = [];
        $parámetros = [];

        foreach (['cliente_id', 'tipo_proceso_id', 'estado_proceso_id', 'responsable_id',
                   'numero_proceso', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_finalizacion'] as $campo) {
            if (isset($datos[$campo])) {
                $campos[] = "$campo = :$campo";
                $parámetros[":$campo"] = $datos[$campo];
            }
        }

        if (empty($campos)) {
            return false; // Nada que actualizar
        }

        // Validar CHECK de fechas a nivel de aplicación si se van a modificar
        if (isset($datos['fecha_inicio']) || isset($datos['fecha_finalizacion'])) {
            $fechaInicio = $datos['fecha_inicio'] ?? null;
            $fechaFinalizacion = $datos['fecha_finalizacion'] ?? null;

            if ($fechaInicio !== null && $fechaInicio !== '' && $fechaFinalizacion !== null && $fechaFinalizacion !== '') {
                $inicioTs = strtotime((string) $fechaInicio);
                $finTs = strtotime((string) $fechaFinalizacion);
                if ($inicioTs !== false && $finTs !== false && $finTs < $inicioTs) {
                    throw new InvalidArgumentException('La fecha de finalización no puede ser anterior a la fecha de inicio.');
                }
            }
        }

        $sql = 'UPDATE procesos SET ' . implode(', ', $campos) . ' WHERE id = :id';
        $parámetros[':id'] = $id;

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute($parámetros);

        return $éxito !== false;
    }

    /**
     * Cambia el estado de un proceso.
     *
     * @param int $id ID del proceso.
     * @param int $nuevoEstado Nuevo estado (1=Activo, 2=Suspendido, 3=Finalizado, 4=Archivado).
     * @return bool true en éxito, false si no existe.
     * @throws InvalidArgumentException si el nuevo estado no es válido.
     */
    public static function cambiarEstado(int $id, int $nuevoEstado): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de proceso debe ser un entero positivo.');
        }
        if (!in_array($nuevoEstado, [1, 2, 3, 4], true)) {
            throw new InvalidArgumentException('El nuevo estado debe ser 1 (Activo), 2 (Suspendido), 3 (Finalizado) o 4 (Archivado).');
        }

        $pdo = Conexion::obtener();

        $sql = 'UPDATE procesos SET estado_proceso_id = :nuevo_estado WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute([':nuevo_estado' => $nuevoEstado, ':id' => $id]);

        return $stmt->rowCount() > 0;
    }
}