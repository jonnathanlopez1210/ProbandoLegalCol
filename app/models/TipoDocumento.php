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
 * Modelo de datos para la entidad 'tipos_documento'.
 *
 * Responsabilidades:
 *   - Listar los tipos de documento disponibles.
 *   - Obtener un tipo de documento por su ID.
 *
 * Datos iniciales (Fase 2, seeds): 8 tipos (Demanda, Contrato, Poder,
 * Sentencia, Notificación, Prueba, Acta, Otro).
 *
 * Restricciones de la BD:
 *   - uq_tipos_documento_nombre: UNIQUE (nombre).
 *   - chk_tipos_documento_estado: estado IN (0,1).
 */
final class TipoDocumento
{
    /**
     * Lista los tipos de documento activos (estado = 1).
     *
     * @return array<Array{id, nombre, descripcion, estado}> Lista de tipos de documento.
     * @throws RuntimeException si falla la consulta.
     */
    public static function listar(): array
    {
        $pdo = Conexion::obtener();

        $sql = 'SELECT id, nombre, descripcion, estado
                FROM tipos_documento
                WHERE estado = 1
                ORDER BY nombre ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene un tipo de documento por su ID.
     *
     * @param int $id Identificador único del tipo de documento.
     * @return array|false Datos del tipo de documento o false si no existe.
     * @throws InvalidArgumentException si $no es un entero positivo.
     */
    public static function obtenerPorId(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de tipo de documento debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        $sql = 'SELECT id, nombre, descripcion, estado
                FROM tipos_documento
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ?: false;
    }
}