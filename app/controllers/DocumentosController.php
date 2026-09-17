<?php

declare(strict_types=1);

namespace Sgdj\Controllers;

use Sgdj\Models\Documento;
use Sgdj\Models\Proceso;
use Sgdj\Models\TipoDocumento;
use Sgdj\Middleware\Auth;
use function Sgdj\helpers\renderizar;
use function Sgdj\helpers\redirigir;
use function Sgdj\helpers\abortar_http;
use function Sgdj\helpers\esc;
use function Sgdj\helpers\flash;
use function Sgdj\helpers\csrf_token;
use function Sgdj\helpers\url;

/**
 * Controlador de documentos.
 *
 * Acciones definidas en routes/web.php:
 *   - 'listar'      => ['GET']
 *   - 'crear'       => ['GET']
 *   - 'guardar'     => ['POST']
 *   - 'editar'      => ['GET']
 *   - 'actualizar'  => ['POST']
 *   - 'eliminar'    => ['POST']
 *   - 'descargar'   => ['GET']
 *
 * El controlador usa los modelos `Documento`, `Proceso` y `TipoDocumento`
 * para operar contra la BD y el sistema de archivos.
 */
final class DocumentosController
{
    public function listar(): void
    {
        $documentos = Documento::listarPorProceso(0);
        renderizar('documentos/lista', ['documentos' => $documentos]);
    }

    public function crear(): void
    {
        $procesos = Proceso::listar();
        $tiposDocumento = TipoDocumento::listar();
        renderizar('documentos/crear', ['procesos' => $procesos, 'tiposDocumento' => $tiposDocumento]);
    }

    public function guardar(): void
    {
        // 1. Verificar autenticación
        if (!Auth::estaAutenticado()) {
            redirigir('login');
        }

        // 2. Verificar CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals(csrf_token(), $_POST['csrf_token'])) {
            abortar_http(400, 'Token de seguridad inválido.');
        }

        // 3. Validar campos del formulario
        $procesoId = (int)($_POST['proceso_id'] ?? 0);
        $tipoDocumentoId = (int)($_POST['tipo_documento_id'] ?? 0);
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $fechaDocumento = $_POST['fecha_documento'] ?? '';

        if ($procesoId <= 0 || $tipoDocumentoId <= 0 || $nombre === '') {
            flash('error', 'Faltan campos obligatorios (proceso, tipo de documento o nombre).');
            redirigir('documentos');
        }

        // 4. Validar proceso existe
        $proceso = Proceso::obtenerPorId($procesoId);
        if ($proceso === false) {
            flash('error', 'El proceso especificado no existe.');
            redirigir('documentos');
        }

        // 5. Validar tipo de documento existe
        $tipoDocumento = TipoDocumento::obtenerPorId($tipoDocumentoId);
        if ($tipoDocumento === false) {
            flash('error', 'El tipo de documento especificado no existe.');
            redirigir('documentos');
        }

        // 6. Validar archivo subido
        $archivoError = $_FILES['archivo']['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($archivoError !== UPLOAD_ERR_OK) {
            $errores = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede la directiva ini de upload_max_filesize.',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el límite definido en HTML.',
                UPLOAD_ERR_PARTIAL => 'La subida del archivo fue parcial.',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo.',
                UPLOAD_ERR_NO_TEMP_DIR => 'Falta la carpeta temporal.',
                UPLOAD_ERR_CANTONEXT => 'No se puede subir el archivo: no hay extensión temporal disponible.',
                UPLOAD_ERR_EXTENSION => 'La extensión de PHP detuvo la subida del archivo.',
            ];
            $mensaje = $errores[$archivoError] ?? 'Error desconocido al subir el archivo.';
            flash('error', $mensaje);
            redirigir('documentos');
        }

        // 5. Validar tamaño
        $tamanoMaximo = APP_MAX_ARCHIVO; // 10 MB
        $tamanoArchivo = $_FILES['archivo']['size'];
        if ($tamanoArchivo > $tamanoMaximo) {
            flash('error', 'El archivo excede el límite máximo de ' . number_format(APP_MAX_ARCHIVO / 1024 / 1024) . ' MB.');
            redirigir('documentos');
        }

        // 6. Validar extensión
        $extensionOriginal = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = UPLOAD_EXTENSIONES_PERMITIDAS;
        if (!in_array($extensionOriginal, $extensionesPermitidas, true)) {
            $permitidas = implode(', ', $extensionesPermitidas);
            flash('error', 'Extensión no permitida. Se permiten: ' . $permitidas);
            redirigir('documentos');
        }

        // 7. Validar MIME real usando finfo_file()
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $_FILES['archivo']['tmp_name']);
        $mimePermitido = false;
        foreach (UPLOAD_MIME_MAP as $ext => $mime) {
            if ($ext === $extensionOriginal) {
                $mimePermitido = $mime;
                break;
            }
        }
        if ($mimeReal !== $mimePermitido) {
            flash('error', 'El tipo MIME real del archivo (' . $mimeReal . ') no coincide con la extensión permitida (' . $mimePermitido . ').');
            redirigir('documentos');
        }
        finfo_close($finfo);

        // 8. Verificar que el archivo temporal existe y es legible
        if (!is_uploaded_file($_FILES['archivo']['tmp_name']) || !is_readable($_FILES['archivo']['tmp_name'])) {
            flash('error', 'El archivo subido no es legible o no existe.');
            redirigir('documentos');
        }

        // 8. Generar nombre físico seguro
        $extensionFisica = $extensionOriginal;
        $nombreFisico = bin2hex(random_bytes(16)) . '.' . $extensionFisica;
        $rutaFisica = UPLOAD_DIRECTORIO . '/' . $nombreFisica;

        // 8. Mover archivo al almacenamiento
        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaFisica)) {
            flash('error', 'No fue posible mover el archivo al almacenamiento.');
            redirigir('documentos');
        }

        // 9. Determinar usuario de sesión
        $usuarioId = Auth::usuarioId(); // Obtener del middleware Auth

        // 10. Registrar metadatos en BD (solo después de guardar el archivo físicamente)
        $resultado = Documento::registrarMetadatos([
            'proceso_id'          => $procesoId,
            'tipo_documento_id'   => $tipoDocumentoId,
            'usuario_id'          => $usuarioId,
            'nombre'              => $nombre,
            'descripcion'         => $descripcion,
            'nombre_archivo'      => $_FILES['archivo']['name'], // nombre original como metadato
            'ruta_archivo'        => $nombreFisica, // nombre físico controlado
            'extension'           => $extensionFisica,
            'tamano'              => $tamanoArchivo,
            'fecha_documento'     => $fechaDocumento,
        ]);

        if ($resultado === false) {
            // Si el INSERT en BD falla, intentar eliminar el archivo físico
            @unlink($rutaFisica);
            flash('error', 'No fue posible registrar los metadatos en la base de datos.');
            redirigir('documentos');
        }

        // 11. Éxito: redirigir
        flash('exito', 'Documento guardado correctamente.');
        redirigir('documentos');
    }

    public function editar(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de documento no válido.');
        }

        $documento = Documento::obtenerPorId($id);

        if ($documento === false) {
            abortar_http(404, 'El documento no existe.');
        }

        $procesos = Proceso::listar();
        $tiposDocumento = TipoDocumento::listar();
        renderizar('documentos/editar', ['documento' => $documento, 'procesos' => $procesos, 'tiposDocumento' => $tiposDocumento]);
    }

    public function actualizar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de documento no válido.');
        }

        $documento = Documento::obtenerPorId($id);

        if ($documento === false) {
            abortar_http(404, 'El documento no existe.');
        }

        // Actualizar solo metadatos (no archivo físico en esta fase)
        $resultado = Documento::actualizarMetadatos($id, [
            'proceso_id'          => (int)($_POST['proceso_id'] ?? $documento['proceso_id']),
            'tipo_documento_id'   => (int)($_POST['tipo_documento_id'] ?? $documento['tipo_documento_id']),
            'usuario_id'          => (int)($_POST['usuario_id'] ?? $documento['usuario_id']),
            'nombre'              => $_POST['nombre'] ?? $documento['nombre'],
            'descripcion'         => $_POST['descripcion'] ?? $documento['descripcion'],
            'fecha_documento'     => $_POST['fecha_documento'] ?? $documento['fecha_documento'],
            'estado'              => (int)($_POST['estado'] ?? $documento['estado']),
        ]);

        if ($resultado === false) {
            flash('error', 'No fue posible actualizar el documento.');
            redirigir('documentos');
        }

        flash('exito', 'Documento actualizado correctamente.');
        redirigir('documentos');
    }

    public function eliminar(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            abortar_http(400, 'ID de documento no válido.');
        }

        $resultado = Documento::cambiarEstado($id, 0);

        if ($resultado === false) {
            flash('error', 'No fue posible desactivar el documento.');
            redirigir('documentos');
        }

        flash('exito', 'Documento desactivado correctamente.');
        redirigir('documentos');
    }

    public function descargar(): void
    {
        // Requerir autenticación
        if (!Auth::estaAutenticado()) {
            redirigir('login');
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            abortar_http(400, 'ID de documento no válido.');
        }

        // Consultar documento en BD
        $documento = Documento::obtenerPorId($id);

        if ($documento === false) {
            abortar_http(404, 'El documento no existe.');
        }

        // Verificar estado (solo activos o según reglas actuales)
        if ($documento['estado'] != 1) {
            abortar_http(403, 'El documento no está disponible para descarga.');
        }

        // Resolver ruta física controlada
        $rutaFisica = UPLOAD_DIRECTORIO . '/' . $documento['ruta_archivo'];

        // Verificar que el archivo existe y está dentro del directorio autorizado
        if (!file_exists($rutaFisica)) {
            abortar_http(404, 'El archivo físico no existe.');
        }

        // Prevenir path traversal: la ruta resolved debe estar dentro de storage/documentos
        $directorioBase = realpath(UPLOAD_DIRECTORIO);
        $rutaResuelta = realpath($rutaFisica);
        if ($rutaResuelta === false || strpos($rutaResuelta, $directorioBase) !== 0) {
            abortar_http(403, 'Acceso no autorizado al archivo.');
        }

        // Establecer headers de descarga
        $extension = $documento['extension'] ?? 'pdf';
        $nombreOriginal = $documento['nombre_archivo'] ?? 'documento';
        $tamano = filesize($rutaFisica);

        header('Content-Type: ' . ($extension === 'pdf' ? 'application/pdf' : 'application/octet-stream'));
        header('Content-Length: ' . $tamano);
        header('Content-Disposition: attachment; filename="' . basename($nombreOriginal) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=0, must-revalidate');

        // Transmitir el archivo
        readfile($rutaFisica);
        exit;
    }
}