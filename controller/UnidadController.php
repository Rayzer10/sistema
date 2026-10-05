<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Upload.php';

class UnidadController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ADMIN);
    }

    public function index()
    {
        $unidadModel = $this->model('Unidad');
        $nivelModel = $this->model('Nivel');

        $niveles = $nivelModel->conUnidadesPublicadas();
        $data = ['niveles' => [], 'menuActivo' => 'contenido', 'titulo' => 'Gestor de Contenido'];

        foreach ($niveles as $n) {
            $unidades = $unidadModel->listarPorNivel($n['id'], false);
            $data['niveles'][] = [
                'nivel'    => $n,
                'unidades' => $unidades
            ];
        }

        $this->render('unidad/index', $data);
    }

    public function form($encodedId = null)
    {
        $unidadModel = $this->model('Unidad');
        $nivelModel = $this->model('Nivel');
        $materiaModel = $this->model('Materia');
        $unidad = null;
        $archivos = [];

        if ($encodedId) {
            $id = $this->decodeId($encodedId);
            $unidad = $unidadModel->findCompleta($id);
            if (!$unidad) {
                $_SESSION['error'] = 'Unidad no encontrada';
                $this->redirect('unidad');
            }
            $archivos = $unidadModel->archivos($id);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titulo'        => trim($_POST['titulo'] ?? ''),
                'descripcion'   => trim($_POST['descripcion'] ?? ''),
                'id_materia'    => (int)($_POST['id_materia'] ?? 0),
                'id_nivel'      => (int)($_POST['id_nivel'] ?? 0),
                'orden'         => (int)($_POST['orden'] ?? 1),
                'estado'        => (int)($_POST['estado'] ?? 1),
                'contenido_html' => $_POST['contenido_html'] ?? null,
            ];

            $errores = [];

            if (empty($data['titulo'])) {
                $errores[] = 'El título es obligatorio';
            } elseif (strlen($data['titulo']) > 150) {
                $errores[] = 'El título no puede exceder 150 caracteres';
            }

            if ($data['id_materia'] <= 0) {
                $errores[] = 'Debe seleccionar una materia';
            } else {
                $materia = $materiaModel->find($data['id_materia']);
                if (!$materia) $errores[] = 'La materia seleccionada no existe';
            }

            if ($data['id_nivel'] <= 0) {
                $errores[] = 'Debe seleccionar un nivel';
            } else {
                $nivel = $nivelModel->find($data['id_nivel']);
                if (!$nivel) $errores[] = 'El nivel seleccionado no existe';
            }

            if ($data['orden'] < 1) {
                $errores[] = 'El orden debe ser un número positivo';
            }

            if (!in_array($data['estado'], [1, 2], true)) {
                $errores[] = 'Estado inválido';
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('unidad/form' . ($encodedId ? '/' . $encodedId : ''));
            }

            // Sanitizar HTML dinamico
            if (!empty($data['contenido_html'])) {
                $data['contenido_html'] = $this->sanitizarHTML($data['contenido_html']);
            }

            if ($unidad) {
                $unidadModel->update($unidad['id'], $data);
                $idUnidad = $unidad['id'];
            } else {
                $idUnidad = $unidadModel->create($data);
            }

            // Manejo de archivos subidos
            $this->manejarArchivos($idUnidad, $archivos);

            $_SESSION['success'] = 'Unidad guardada exitosamente';
            $this->redirect('unidad');
        }

        $this->render('unidad/form', [
            'titulo'     => ($unidad ? 'Editar' : 'Nueva') . ' Unidad',
            'menuActivo' => 'contenido',
            'unidad'     => $unidad,
            'archivos'   => $archivos,
            'niveles'    => $nivelModel->todos(),
            'materias'   => $materiaModel->listarMaterias(),
            'encodedId'  => $encodedId
        ]);
    }

    public function eliminar($encodedId)
    {
        $unidadModel = $this->model('Unidad');
        $id = $this->decodeId($encodedId);

        $archivos = $unidadModel->archivos($id);
        foreach ($archivos as $a) {
            Upload::eliminar($a['ruta']);
        }

        $unidadModel->delete($id);
        $_SESSION['success'] = 'Unidad eliminada exitosamente';
        $this->redirect('unidad');
    }

    public function eliminarArchivo($encodedIdUnidad, $encodedIdArchivo)
    {
        $unidadModel = $this->model('Unidad');
        $idUnidad = $this->decodeId($encodedIdUnidad);
        $idArchivo = $this->decodeId($encodedIdArchivo);

        $sql = "SELECT * FROM unidad_archivo WHERE id = ? AND id_unidad = ?";
        $archivo = $this->db->fetch($sql, [$idArchivo, $idUnidad]);

        if ($archivo) {
            Upload::eliminar($archivo['ruta']);
            $sqlDel = "DELETE FROM unidad_archivo WHERE id = ?";
            $this->db->execute($sqlDel, [$idArchivo]);
            $_SESSION['success'] = 'Archivo eliminado';
        }

        $this->redirect('unidad/form/' . $encodedIdUnidad);
    }

    /**
     * Procesa los archivos subidos en el request.
     * Reemplaza archivos existentes del mismo tipo.
     */
    private function manejarArchivos($idUnidad, array $archivosExistentes)
    {
        $tiposArchivo = ['mp4', 'pdf', 'pptx'];

        foreach ($tiposArchivo as $tipo) {
            $campo = 'archivo_' . $tipo;

            if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $resultado = Upload::subir($_FILES[$campo], $tipo);
            if (!$resultado['ok']) {
                $_SESSION['error'] = "Error al subir $tipo: " . $resultado['error'];
                continue;
            }

            // Eliminar archivo anterior del mismo tipo
            foreach ($archivosExistentes as $a) {
                if ($a['tipo'] === $tipo) {
                    Upload::eliminar($a['ruta']);
                    $this->db->execute("DELETE FROM unidad_archivo WHERE id = ?", [$a['id']]);
                }
            }

            $sql = "INSERT INTO unidad_archivo (id_unidad, tipo, ruta, nombre_original, tamano_bytes) VALUES (?, ?, ?, ?, ?)";
            $this->db->execute($sql, [
                $idUnidad,
                $tipo,
                $resultado['ruta'],
                $resultado['nombre_original'],
                $resultado['tamano']
            ]);
        }
    }

    private function sanitizarHTML($html)
    {
        // Sanitizacion basica: permitir etiquetas seguras
        $permitidas = '<p><br><b><strong><i><em><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><code><pre><blockquote><table><thead><tbody><tr><th><td><hr><img>';
        $html = strip_tags($html, $permitidas);

        // Eliminar javascript: y on* attributes
        $html = preg_replace('/javascript:/i', '', $html);
        $html = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $html);

        return $html;
    }
}
