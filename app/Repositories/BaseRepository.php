<?php

declare(strict_types=1);

namespace Sgdj\Repositories;

use PDO;
use Sgdj\Database\Conexion;

/**
 * Base de los repositorios: expone la conexión PDO compartida.
 * La conexión se obtiene de forma diferida: solo cuando se realiza la primera
 * operación de acceso a datos.
 */
abstract class BaseRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Conexion::obtener();
    }
}