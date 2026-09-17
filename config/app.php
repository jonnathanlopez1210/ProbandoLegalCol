<?php

declare(strict_types=1);

/**
 * Configuración general de la aplicación.
 *
 * Archivo exclusivamente con constantes de configuración.
 * No contiene lógica de negocio ni credenciales.
 * Las credenciales se definen en config/database.php (archivo local, excluido por .gitignore).
 */

define('APP_NAME', 'Gestor Documental Jurídico');
define('APP_SHORT_NAME', 'SGDJ');
define('APP_VERSION', '1.1.0');
define('APP_PHASE', 'Fase 3 - Desarrollo de la aplicación');

/**
 * Entorno de ejecución: 'development' o 'production'.
 * En 'development' se muestran detalles técnicos de los errores.
 */
define('APP_ENV', 'development');

define('APP_CHARSET', 'UTF-8');

define('ROOT_PATH', dirname(__DIR__));
define('STORAGE_PATH', ROOT_PATH . '/storage/documentos');

/**
 * Tamaño máximo admitido para documentos subidos (en bytes): 10 MB.
 */
define('APP_MAX_ARCHIVO', 10 * 1024 * 1024);

/**
 * Extensiones permitidas para documentos subidos.
 */
define('APP_EXTENSIONES_ARCHIVO', ['pdf', 'doc', 'docx', 'xls', 'xlsx']);

/**
 * Configuración de subida de archivos.
 */
define('UPLOAD_MAX_SIZE', APP_MAX_ARCHIVO);
define('UPLOAD_EXTENSIONES_PERMITIDAS', APP_EXTENSIONES_ARCHIVO);
define('UPLOAD_DIRECTORIO', STORAGE_PATH);
define('UPLOAD_MIME_MAP', [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
]);

/**
 * URL base de la aplicación (sin barra final).
 * Ejemplo: http://localhost/LegalCol2.0/public
 */
define('BASE_URL', 'http://localhost/LegalCol2.0 - copia/public');
