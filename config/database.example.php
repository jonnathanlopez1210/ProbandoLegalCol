<?php

declare(strict_types=1);

/**
 * Plantilla de configuración de conexión a MySQL.
 *
 * IMPORTANTE:
 * 1. Copia este archivo como "config/database.php".
 * 2. Ajústalo según tu entorno local (XAMPP o Laragon).
 * 3. "config/database.php" está excluido por .gitignore y nunca debe subirse al repositorio.
 * 4. No utilices credenciales reales: este proyecto aún no define la base de datos (Fase 2).
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'gestor_documental_juridico');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_CHARSET', 'utf8mb4');