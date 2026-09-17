<?php

declare(strict_types=1);

namespace Sgdj\Services;

use Sgdj\Middleware\Auth;
use Sgdj\Repositories\UsuarioRepository;

/**
 * Responsable de la lógica de autenticación: verificar credenciales y
 * gestionar la sesión autenticada.
 */
final class AuthService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository
    ) {
    }

    /**
     * Intenta autenticar al usuario.
     *
     * Devuelve un arreglo con dos claves:
     *   - 'autenticado' => bool
     *   - 'motivo'      => 'ok' | 'credenciales' | 'inactivo'
     *     ('inactivo' solo se informa si el correo y la contraseña son
     *      correctos pero el usuario está desactivado; no se filtra correo
     *      existente por motivos de seguridad).
     */
    public function autenticar(string $correo, string $password): array
    {
        $correo   = normalizar($correo);
        $usuario  = $this->usuarioRepository->buscarPorCorreo(strtolower($correo));

        if ($usuario === null) {
            return ['autenticado' => false, 'motivo' => 'credenciales'];
        }

        if (!password_verify($password, $usuario['password'])) {
            return ['autenticado' => false, 'motivo' => 'credenciales'];
        }

        if ((int) $usuario['estado'] !== 1) {
            return ['autenticado' => false, 'motivo' => 'inactivo'];
        }

        Auth::iniciarSesion($usuario);

        return ['autenticado' => true, 'motivo' => 'ok'];
    }

    public function cerrarSesion(): void
    {
        Auth::cerrar();
    }
}