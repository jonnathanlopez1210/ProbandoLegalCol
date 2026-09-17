<?php

declare(strict_types=1);

namespace Sgdj\Models;

use PDO;
use PDOException;
use RuntimeException;
use InvalidArgumentException;
use Sgdj\Database\Conexion;
use function Sgdj\helpers\esc;
use function Sgdj\helpers\flash;
/**
 * Modelo de datos para la entidad 'usuarios'.
 *
 * Responsabilidades:
 *   - Buscar usuario por correo (único).
 *   - Obtener usuario por ID.
 *   - Validar presencia de datos mínimos.
 *   - Nunca manejar contraseñas en claro; el modelo devuelve el hash
 *     y quien lo compara es la capa de controlador con password_verify().
 *
 * La conexión PDO se obtiene mediante la clase existente Conexion::obtener().
 */
final class Usuario
{
    /**
     * Busca un usuario por su dirección de correo electrónico.
     *
     * @param string $correo Correo electrónico del usuario (único).
     * @return array|false Array con los datos del usuario o false si no existe.
     * @throws RuntimeException si falla la conexión o la consulta.
     */
    public static function buscarPorCorreo(string $correo): array|false
    {
        $pdo = Conexion::obtener();

        $sql = 'SELECT id, rol_id, nombres, apellidos, correo, password, estado, created_at, updated_at
                FROM usuarios
                WHERE correo = :correo';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario === false) {
            return false;
        }

        // Validación mínima: el usuario debe estar activo (estado = 1)
        // según la restricción chk_usuarios_estado de la BD.
        if (!isset($usuario['estado']) || $usuario['estado'] !== 1) {
            return false;
        }

        return $usuario;
    }

    /**
     * Obtiene los datos completos de un usuario por su ID.
     *
     * @param int $id Identificador único del usuario.
     * @return array|false Datos del usuario o false si no existe.
     * @throws InvalidArgumentException si $id no es un entero positivo.
     */
    public static function obtenerPorId(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de usuario debe ser un entero positivo.');
        }

        $pdo = Conexion::obtener();

        $sql = 'SELECT id, rol_id, nombres, apellidos, correo, password, estado, created_at, updated_at
                FROM usuarios
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    /**
     * Valida que los datos mínimos de un usuario sean correctos para el registro.
     *
     * Esta validación la realiza la capa de controlador; el modelo sólo
     * expone la estructura esperada.
     *
     * @param array $datos Datos crudos de entrada (nombres, apellidos, correo, password, rol_id).
     * @return array Datos validados y listos para insertar.
     */
    public static function validarDatos(array $datos): array
    {
        $validado = [];

        // Nombre completo (nombres y apellidos obligatorios)
        $validado['nombres'] = trim($datos['nombres'] ?? '') !== '' ? trim($datos['nombres']) : null;
        $validado['apellidos'] = trim($datos['apellidos'] ?? '') !== '' ? trim($datos['apellidos']) : null;

        // Correo electrónico (único, obligatorio)
        $validado['correo'] = trim($datos['correo'] ?? '') !== '' ? trim(strtolower($datos['correo'])) : null;
        
        // Contraseña (obligatoria en creación; vacía significa "mantener la existente" en edición)
        $validado['password'] = $datos['password'] ?? '';

        // Rol ID debe ser entero positivo
        $validado['rol_id'] = isset($datos['rol_id']) && is_numeric($datos['rol_id']) && $datos['rol_id'] > 0
            ? (int) $datos['rol_id']
            : null;

        return $validado;
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     *
     * Si se proporciona una contraseña, se almacena su hash (password_hash con DEFAULT).
     * Si no se proporciona contraseña (caso de edición), el método lanza una excepción
     * porque la creación requiere contraseña obligatoria.
     *
     * @param array $datos Arreglo con: nombres, apellidos, correo, password, rol_id
     * @return int|false ID del nuevo usuario insertado o false en fallo
     * @throws InvalidArgumentException si faltan datos obligatorios
     * @throws RuntimeException si falla la inserción
     */
    public static function crear(array $datos): int|false
    {
        $validado = self::validarDatos($datos);

        if ($validado['nombres'] === null) {
            throw new InvalidArgumentException('Los nombres son obligatorios.');
        }
        if ($validado['apellidos'] === null) {
            throw new InvalidArgumentException('Los apellidos son obligatorios.');
        }
        if ($validado['correo'] === null) {
            throw new InvalidArgumentException('El correo es obligatorio.');
        }
        if ($validado['password'] === '' || $validado['password'] === null) {
            throw new InvalidArgumentException('La contraseña es obligatoria para la creación de un usuario.');
        }
        if ($validado['rol_id'] === null) {
            throw new InvalidArgumentException('El rol es obligatorio.');
        }

        // Verificar que el correo no exista ya (validación única)
        $existente = self::buscarPorCorreo($validado['correo']);
        if ($existente !== false) {
            throw new InvalidArgumentException('El correo electrónico ya está registrado.');
        }

        // Verificar que el rol existe
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT id FROM roles WHERE id = :rol_id');
        $stmt->execute(['rol_id' => $validado['rol_id']]);
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rol === false) {
            throw new InvalidArgumentException('El rol especificado no existe.');
        }

        // Hashear la contraseña
        $hashPassword = password_hash($validado['password'], PASSWORD_DEFAULT);

        // Insertar usuario
        $sql = 'INSERT INTO usuarios (rol_id, nombres, apellidos, correo, password, estado)
                VALUES (:rol_id, :nombres, :apellidos, :correo, :password, 1)';

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute([
            ':rol_id' => $validado['rol_id'],
            ':nombres' => $validado['nombres'],
            ':apellidos' => $validado['apellidos'],
            ':correo' => $validado['correo'],
            ':password' => $hashPassword,
        ]);

        if (!$éxito) {
            return false;
        }

        return $pdo->lastInsertId();
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * - Los campos nombres, apellidos, correo y rol_id se actualizan si vienen definidos.
     * - Si se proporciona una nueva contraseña, se reemplaza el hash existente.
     * - Si no se proporciona contraseña, se conserva la contraseña actual.
     *
     * @param int $id ID del usuario a actualizar
     * @param array $datos Arreglo con campos opcionales: nombres, apellidos, correo, password, rol_id
     * @return bool true en éxito, false si el usuario no existe
     * @throws InvalidArgumentException si el ID no es válido o el correo ya existe
     */
    public static function actualizar(int $id, array $datos): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de usuario debe ser un entero positivo.');
        }

        // Validar que el usuario existe
        $usuario = self::obtenerPorId($id);
        if ($usuario === false) {
            return false;
        }

        // Usar los datos validados; si password está vacío, conservar la existente
        $passwordActual = $usuario['password'] ?? '';
        $datosValidados = self::validarDatos($datos);

        // Determinar el password a usar: nuevo si viene, si no el actual
        $passwordFinal = $datosValidados['password'] !== '' ? password_hash($datosValidados['password'], PASSWORD_DEFAULT) : $passwordActual;

        // Verificar unicidad de correo si viene cambiado
        if ($datosValidados['correo'] !== null && $datosValidados['correo'] !== $usuario['correo']) {
            $existente = self::buscarPorCorreo($datosValidados['correo']);
            if ($existente !== false) {
                throw new InvalidArgumentException('El correo electrónico ya está registrado por otro usuario.');
            }
        }

        // Si el rol viene cambiando, verificar que el nuevo rol existe
        if (isset($datosValidados['rol_id']) && $datosValidados['rol_id'] !== $usuario['rol_id']) {
            $pdo = Conexion::obtener();
            $stmt = $pdo->prepare('SELECT id FROM roles WHERE id = :rol_id');
            $stmt->execute(['rol_id' => $datosValidados['rol_id']]);
            $rol = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rol === false) {
                throw new InvalidArgumentException('El rol especificado no existe.');
            }
        }

        $pdo = Conexion::obtener();

        $sql = 'UPDATE usuarios SET 
                rol_id = COALESCE(:rol_id, rol_id),
                nombres = COALESCE(:nombres, nombres),
                apellidos = COALESCE(:apellidos, apellidos),
                correo = COALESCE(:correo, correo),
                password = :password,
                updated_at = NOW()
              WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute([
            ':id' => $id,
            ':rol_id' => $datosValidados['rol_id'] ?? null,
            ':nombres' => $datosValidados['nombres'] ?? null,
            ':apellidos' => $datosValidados['apellidos'] ?? null,
            ':correo' => $datosValidados['correo'] ?? null,
            ':password' => $passwordFinal,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Lista los usuarios registrados.
     *
     * Devuelve todos los usuarios activos (estado = 1) o todos si no hay filtros.
     * El controlador decidirá qué mostrar según el contexto.
     *
     * @return array<Array{id, rol_id, nombres, apellidos, correo, password, estado, created_at, updated_at}> Lista de usuarios.
     * @throws RuntimeException si falla la consulta.
     */
    public static function listar(): array
    {
        $pdo = Conexion::obtener();

        $sql = 'SELECT id, rol_id, nombres, apellidos, correo, password, estado, created_at, updated_at
                FROM usuarios
                ORDER BY nombres ASC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Cambia el estado de un usuario (activar/desactivar).
     *
     * @param int $id ID del usuario
     * @param int $nuevoEstado 1 = activo, 0 = inactivo
     * @return bool true en éxito, false si el usuario no existe
     * @throws InvalidArgumentException si el estado no es 0 o 1
     */
    public static function cambiarEstado(int $id, int $nuevoEstado): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de usuario debe ser un entero positivo.');
        }
        if (!in_array($nuevoEstado, [0, 1], true)) {
            throw new InvalidArgumentException('El estado debe ser 0 (inactivo) o 1 (activo).');
        }

        $pdo = Conexion::obtener();

        // Verificar que el usuario existe antes de actualizar
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
            return false;
        }

        $sql = 'UPDATE usuarios SET estado = :nuevo_estado, updated_at = NOW() WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $éxito = $stmt->execute([':nuevo_estado' => $nuevoEstado, ':id' => $id]);

        return $stmt->rowCount() > 0;
    }
}