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
 * Modelo de datos para la entidad 'documentos'.
 *
 * Responsabilidades:
 *   - Listar documentos por proceso (y opcionalmente por estado).
 *   - Obtener un documento por su ID.
 *   - Registrar nuevos metadatos de documento.
 *   - Actualizar metadatos de un documento existente.
 *   - Cambiar el estado de un documento.
 *   - Buscar documentos con filtros múltiples.
 *
 * Relaciones de clave foránea respectadas:
 *   - proceso_id  → procesos (FK)
 *   - tipo_documento_id → tipos_documento (FK)
 *   - usuario_id      → usuarios (FK)
 *
 * Restricciones de la BD:
 *   - chk_documentos_estado: estado IN (0,1).
 *   - FK a procesos, tipos_documento, usuarios.
 */
final class Documento
{
    /**
     * Lista los documentos de un proceso específico, opcionalmente filtrados por estado.
     *
     * @param int $procesoId ID del proceso al que pertenecen los documentos.
     * @param int|null $estado 1=activo, 0=inactivo. Si es null, lista todos.
     * @return array<Array{id, proceso_id, tipo_documento_id, usuario_id, nombre, descripcion, nombre_archivo, ruta_archivo, extension, tamano, fecha_documento, estado}> Lista de documentos.
     * @throws RuntimeException si falla la consulta.
     */
    public static function listarPorProceso(int $procesoId, ?int $estado = null): array
    {
        if ($procesoId <= 0) {
            throw new InvalidArgumentException('El ID del proceso debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        $sql = 'SELECT d.id, d.proceso_id, d.tipo_documento_id, d.usuario_id,
                        d.nombre, d.descripcion, d.nombre_archivo, d.ruta_archivo,
                        d.extension, d.tamano, d.fecha_documento, d.estado,
                        t.nombre AS tipo_documento_nombre,
                        u.nombres AS usuario_nombres,
                        u.apellidos AS usuario_apellidos
                FROM documentos d
                LEFT JOIN tipos_documento t ON d.tipo_documento_id = t.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.proceso_id = :proceso_id';

        $condiciones = [];
        $parámetros = [':proceso_id' => $procesoId];

        if ($estado !== null) {
            $condiciones[] = 'd.estado = :estado';
            $parámetros[':estado'] = $estado;
        }

        if (!empty($condiciones)) {
            $sql .= ' AND ' . implode(' AND ', $condiciones);
        }

        $sql .= ' ORDER BY d.fecha_documento DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parámetros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene los datos de un documento por su ID.
     *
     * @param int $id Identificador único del documento.
     * @return array|false Datos del documento o false si no existe.
     * @throws InvalidArgumentException si $no es un entero positivo.
     */
    public static function obtenerPorId(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de documento debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        $sql = 'SELECT d.id, d.proceso_id, d.tipo_documento_id, d.usuario_id,
                        d.nombre, d.descripcion, d.nombre_archivo, d.ruta_archivo,
                        d.extension, d.tamano, d.fecha_documento, d.estado,
                        t.nombre AS tipo_documento_nombre,
                        u.nombres AS usuario_nombres,
                        u.apellidos AS usuario_apellidos
                FROM documentos d
                LEFT JOIN tipos_documento t ON d.tipo_documento_id = t.id
                LEFT JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ?: false;
    }

    /**
     * Registra nuevos metadatos de documento en la base de datos.
     *
     * Los datos obligatorios según el esquema de la BD:
     *   - proceso_id: ID de proceso existente.
     *   - tipo_documento_id: ID de tipo de documento existente.
     *   - usuario_id: ID de usuario existente (quien registra el documento).
     *   - nombre: nombre del documento (cadena no vacía).
     *   - nombre_archivo: nombre físico del archivo.
     *   - ruta_archivo: ruta donde reside el archivo en storage/documentos/.
     *   - extension: extensión del archivo (ej. 'pdf', 'docx').
     *   - tamano: tamaño en bytes (entero positivo).
     *   - fecha_documento: fecha del documento (formato DATE/DATETIME).
     *
     * @param array $datos Arreglo asociativo con los metadatos del documento.
     * @return int|false ID del nuevo documento insertado, o false en caso de fallo.
     * @throws InvalidArgumentException si los datos obligatorios son inválidos.
     */
    public static function registrarMetadatos(array $datos): int|false
    {
        // Validación obligatoria
        $procesoId = $datos['proceso_id'] ?? null;
        $tipoDocumentoId = $datos['tipo_documento_id'] ?? null;
        $usuarioId = $datos['usuario_id'] ?? null;
        $nombre = $datos['nombre'] ?? '';
        $nombreArchivo = $datos['nombre_archivo'] ?? '';
        $rutaArchivo = $datos['ruta_archivo'] ?? '';
        $extension = $datos['extension'] ?? '';
        $tamano = $datos['tamano'] ?? null;
        $fechaDocumento = $datos['fecha_documento'] ?? '';

        if ($procesoId === null || $procesoId <= 0) {
            throw new InvalidArgumentException('El proceso_id es obligatorio y debe ser un entero positivo.');
        }
        if ($tipoDocumentoId === null || $tipoDocumentoId <= 0) {
            throw new InvalidArgumentException('El tipo_documento_id es obligatorio y debe ser un entero positivo.');
        }
        if ($usuarioId === null || $usuarioId <= 0) {
            throw new InvalidArgumentException('El usuario_id es obligatorio y debe ser un entero positivo.');
        }
        if ($nombre === '') {
            throw new InvalidArgumentException('El nombre del documento es obligatorio.');
        }
        if ($nombreArchivo === '') {
            throw new InvalidArgumentException('El nombre del archivo es obligatorio.');
        }
        if ($rutaArchivo === '') {
            throw new InvalidArgumentException('La ruta del archivo es obligatoria.');
        }
        if ($extension === '') {
            throw new InvalidArgumentException('La extensión es obligatoria.');
        }
        if ($tamano === null || $tamano <= 0) {
            throw new InvalidArgumentException('El tamano debe ser un entero positivo.');
        }
        if ($fechaDocumento === '') {
            throw new InvalidArgumentException('La fecha del documento es obligatoria.');
        }

        // Verificar que las FK existan (opcional pero recomendado para integridad)
        $pdo = Conexion::obtener();

        // Usar transacción para garantizar la integridad de las FK
        try {
            $pdo->beginTransaction();

            $sql = 'INSERT INTO documentos (proceso_id, tipo_documento_id, usuario_id,
                            nombre, descripcion, nombre_archivo, ruta_archivo,
                            extension, tamano, fecha_documento, estado)
                    VALUES (:proceso_id, :tipo_documento_id, :usuario_id,
                            :nombre, :descripcion, :nombre_archivo, :ruta_archivo,
                            :extension, :tamano, :fecha_documento, 1)';

            $stmt = $pdo->prepare($sql);
            $éxito = $stmt->execute([
                ':proceso_id'          => $procesoId,
                ':tipo_documento_id'   => $tipoDocumentoId,
                ':usuario_id'          => $usuarioId,
                ':nombre'              => $nombre,
                ':descripcion'         => $datos['descripcion'] ?? null,
                ':nombre_archivo'      => $nombreArchivo,
                ':ruta_archivo'        => $rutaArchivo,
                ':extension'           => $extension,
                ':tamano'              => $tamano,
                ':fecha_documento'     => $fechaDocumento,
            ]);

            if (!$éxito) {
                $pdo->rollBack();
                return false;
            }

            $id = $pdo->lastInsertId();

            $pdo->commit();
            return $id;
        } catch (PDOException $e) {
            // FK violada (proceso/tipo_documento/usuario inexistente)
            if ($e->getCode() === '23000') {
                $pdo->rollBack();
                return false;
            }
            throw new RuntimeException('Error al registrar metadatos del documento: ' . esc($e->getMessage()));
        }
    }

    /**
     * Actualiza los metadatos de un documento existente.
     *
     * Solo actualiza los campos que vienen en el arreglo $datos.
     * Los campos no proporcionados se dejan sin cambios.
     *
     * @param int $id ID del documento a actualizar.
     * @param array $datos Arreglo asociativo con los campos a actualizar.
     * @return bool true en éxito, false en fallo.
     * @throws InvalidArgumentException si el ID no es válido.
     */
    public static function actualizarMetadatos(int $id, array $datos): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de documento debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        // Construir SET dinámico solo con campos que vienen en $datos
        $campos = [];
        $parámetros = [];

        foreach (['proceso_id', 'tipo_documento_id', 'usuario_id', 'nombre', 'descripcion',
                   'nombre_archivo', 'ruta_archivo', 'extension', 'tamano', 'fecha_documento', 'estado'] as $campo) {
            if (isset($datos[$campo])) {
                $campos[] = "$campo = :$campo";
                $parámetros[":$campo"] = $datos[$campo];
            }
        }

        if (empty($campos)) {
            return false; // Nada que actualizar
        }

        // Validar CHECK de estado (0 o 1) si viene en los datos
        if (isset($datos['estado']) && !in_array($datos['estado'], [0, 1], true)) {
            throw new InvalidArgumentException('El estado debe ser 0 o 1.');
        }

        $sql = 'UPDATE documentos SET ' . implode(', ', $campos) . ' WHERE id = :id';
        $parámetros[':id'] = $id;

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute($parámetros);

        return $éxito !== false;
    }

    /**
     * Cambia el estado de un documento.
     *
     * @param int $id ID del documento.
     * @param int $nuevoEstado Nuevo estado (1=activo, 0=inactivo).
     * @return bool true en éxito, false si no existe.
     * @throws InvalidArgumentException si el nuevo estado no es válido.
     */
    public static function cambiarEstado(int $id, int $nuevoEstado): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de documento debe ser un entero positivo.');
        }
        if (!in_array($nuevoEstado, [0, 1], true)) {
            throw new InvalidArgumentException('El estado debe ser 0 (inactivo) o 1 (activo).');
        }

        $pdo = Conexion::obtener();

        $sql = 'UPDATE documentos SET estado = :nuevo_estado WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute([':nuevo_estado' => $nuevoEstado, ':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Busca documentos con filtros múltiples.
     *
     * Los filtros aplicables son:
     *   - numero_proceso: búsqueda por número de proceso (mediante JOIN con procesos)
     *   - nombre: búsqueda por nombre/tiítulo del documento
     *   - tipo_documento_id: filtrar por tipo de documento
     *   - estado: filtrar por estado del documento (1=activo, 0=inactivo)
     *
     * Los filtros vacíos o nulos no se incluyen en la consulta.
     *
     * @param string|null $numeroProceso Número de proceso para buscar (ej. '2026-00125')
     * @param string|null $nombre Nombre o título del documento para buscar
     * @param int|null $tipoDocumentoId ID del tipo de documento para filtrar
     * @param int|null $estado Estado del documento (1=activo, 0=inactivo)
     * @return array<Array{id, proceso_id, tipo_documento_id, usuario_id, nombre, descripcion, nombre_archivo, ruta_archivo, extension, tamano, fecha_documento, estado, numero_proceso, tipo_nombre, estado_nombre}> Lista de documentos que coinciden con los filtros.
     * @throws RuntimeException si falla la consulta.
     */
    public static function buscar(string $numeroProceso = null, string $nombre = null, ?int $tipoDocumentoId = null, ?int $estado = null): array
    {
        $pdo = Conexion::obtener();

        // Construir la consulta con JOINs necesarios
        $sql = 'SELECT d.id, d.proceso_id, d.tipo_documento_id, d.usuario_id,
                        d.nombre, d.descripcion, d.nombre_archivo, d.ruta_archivo,
                        d.extension, d.tamano, d.fecha_documento, d.estado,
                        p.numero_proceso,
                        t.nombre AS tipo_documento_nombre';

        $condiciones = [];
        $parámetros = [];

        // Filtrar por número de proceso (a través de la relación proceso)
        if ($numeroProceso !== null && $numeroProceso !== '') {
            $condiciones[] = 'p.numero_proceso LIKE :numero_proceso';
            $parámetros[':numero_proceso'] = '%' . $numeroProceso . '%';
        }

        // Filtrar por nombre del documento
        if ($nombre !== null && $nombre !== '') {
            $condiciones[] = 'd.nombre LIKE :nombre';
            $parámetros[':nombre'] = '%' . $nombre . '%';
        }

        // Filtrar por tipo de documento
        if ($tipoDocumentoId !== null) {
            $condiciones[] = 'd.tipo_documento_id = :tipo_documento_id';
            $parámetros[':tipo_documento_id'] = $tipoDocumentoId;
        }

        // Filtrar por estado del documento
        if ($estado !== null) {
            $condiciones[] = 'd.estado = :estado';
            $parámetros[':estado'] = $estado;
        }

        // Unimos con procesos y tipos_documento para obtener los nombres
        $sql .= ' FROM documentos d
                  LEFT JOIN procesos p ON d.proceso_id = p.id
                  LEFT JOIN tipos_documento t ON d.tipo_documento_id = t.id';

        if (!empty($condiciones)) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }

        $sql .= ' ORDER BY d.fecha_documento DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($parámetros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}