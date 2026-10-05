<?php
/**
 * Upload - Helper para manejo de archivos subidos al servidor.
 *
 * Tipos soportados: mp4, pdf, pptx, html
 *   - mp4, pdf, pptx: se almacenan como archivo fisico en /uploads/unidades/{tipo}/
 *   - html: no se sube archivo, el contenido se guarda directamente en unidad.contenido_html
 */
class Upload
{
    const TIPOS_PERMITIDOS = ['mp4', 'pdf', 'pptx', 'html'];
    const MAX_SIZE_BYTES    = 104857600; // 100 MB

    private static $mimeMap = [
        'mp4'  => ['video/mp4'],
        'pdf'  => ['application/pdf'],
        'pptx' => [
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-powerpoint',
            'application/octet-stream'
        ],
        'html' => ['text/html', 'application/octet-stream']
    ];

    /**
     * Sube un archivo y devuelve la ruta relativa o null si falla.
     *
     * @param array $file  Entrada de $_FILES
     * @param string $tipo Tipo destino: mp4, pdf, pptx
     * @return array ['ok' => bool, 'ruta' => string|null, 'error' => string|null, 'nombre' => string|null, 'tamano' => int]
     */
    public static function subir($file, $tipo)
    {
        if (!in_array($tipo, ['mp4', 'pdf', 'pptx'], true)) {
            return ['ok' => false, 'error' => 'Tipo de archivo no soportado para upload.'];
        }

        if (!isset($file['error']) || is_array($file['error'])) {
            return ['ok' => false, 'error' => 'Parámetros de archivo inválidos.'];
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK: break;
            case UPLOAD_ERR_NO_FILE: return ['ok' => false, 'error' => 'No se envió ningún archivo.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['ok' => false, 'error' => 'El archivo excede el tamaño máximo permitido.'];
            default:
                return ['ok' => false, 'error' => 'Error desconocido al subir el archivo.'];
        }

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            return ['ok' => false, 'error' => 'El archivo excede el tamaño máximo permitido (100 MB).'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== $tipo) {
            return ['ok' => false, 'error' => "La extensión del archivo debe ser .$tipo"];
        }

        // Validación de MIME (best-effort, el .htaccess ya bloquea ejecución)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $mimesValidos = self::$mimeMap[$tipo] ?? [];
        if (!in_array($mime, $mimesValidos, true)) {
            return ['ok' => false, 'error' => "Tipo MIME no permitido ($mime)."];
        }

        $directorio = __DIR__ . '/../uploads/unidades/' . $tipo;
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombreSeguro = uniqid('u' . $tipo . '_', true) . '.' . $ext;
        $destino = $directorio . '/' . $nombreSeguro;

        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            return ['ok' => false, 'error' => 'No se pudo guardar el archivo en el servidor.'];
        }

        $rutaRelativa = 'uploads/unidades/' . $tipo . '/' . $nombreSeguro;

        return [
            'ok'              => true,
            'ruta'            => $rutaRelativa,
            'nombre_original' => $file['name'],
            'tamano'          => $file['size'],
            'error'           => null
        ];
    }

    /**
     * Elimina un archivo fisico del servidor
     */
    public static function eliminar($rutaRelativa)
    {
        if (empty($rutaRelativa)) return false;
        $rutaCompleta = __DIR__ . '/../' . $rutaRelativa;
        if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
            return @unlink($rutaCompleta);
        }
        return false;
    }
}
