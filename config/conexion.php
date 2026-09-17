<?php

declare(strict_types=1);

namespace Sgdj\Database;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Conexión a MySQL mediante PDO (patrón singleton).
 *
 * Propiedades de conexión seguras para su uso posterior:
 *  - Consultas preparadas reales (ATTR_EMULATE_PREPARES => false).
 *  - Errores controlados mediante excepciones PDOException.
 *  - Detalles técnicos ocultos en producción y visibles solo en desarrollo.
 */
final class Conexion
{
    private static ?PDO $instancia = null;

    /**
     * Evita la instanciación directa: se usa únicamente Conexion::obtener().
     */
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Devuelve la instancia única de PDO, creándola la primera vez.
     */
    public static function obtener(): PDO
    {
        if (self::$instancia === null) {
            self::$instancia = self::crear();
        }

        return self::$instancia;
    }

    private static function crear(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            return new PDO($dsn, DB_USER, DB_PASSWORD, $opciones);
        } catch (PDOException $e) {
            error_log('[SGDJ][Conexion] ' . $e->getMessage());
            throw new RuntimeException(self::mensajeControlado($e));
        }
    }

    private static function mensajeControlado(PDOException $e): string
    {
        $mensaje = 'No se pudo establecer la conexión con la base de datos.';

        if (APP_ENV === 'development') {
            $mensaje .= ' Detalle técnico: ' . $e->getMessage();
        }

        return $mensaje;
    }
}