<?php

declare(strict_types=1);

namespace Sgdj\Helpers;

/**
 * Validaciones básicas de entrada reutilizables en los servicios.
 */
final class Validador
{
    public static function requerido(string $valor): bool
    {
        return trim($valor) !== '';
    }

    public static function correo(string $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function longitud(string $valor, int $min, int $max): bool
    {
        $longitud = mb_strlen($valor);

        return $longitud >= $min && $longitud <= $max;
    }

    public static function numero(string $valor): bool
    {
        return is_numeric($valor);
    }

    public static function enteroPositivo(string $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }

    public static function enLista(string $valor, array $opciones): bool
    {
        return in_array($valor, $opciones, true);
    }

    public static function fecha(string $valor): bool
    {
        $fecha = date_create_from_format('Y-m-d', $valor);

        return $fecha !== false && $fecha->format('Y-m-d') === $valor;
    }

    /**
     * Comprueba que fechaFin sea posterior o igual a fechaInicio.
     * Devuelve true si cualquiera de las dos no puede interpretarse.
     */
    public static function fechaPosteriorOIgual(string $fechaInicio, string $fechaFin): bool
    {
        $inicio = strtotime($fechaInicio);
        $fin    = strtotime($fechaFin);

        if ($inicio === false || $fin === false) {
            return true;
        }

        return $fin >= $inicio;
    }
}