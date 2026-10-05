<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Upload.php';

class EscenarioController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
    }

    public function index()
    {
        $asigModel = $this->model('AsignacionEscenario');
        $asignaciones = $asigModel->listarPorEstudiante(Auth::id());

        $this->render('escenario/index', [
            'titulo'       => 'Simulador de Escenarios',
            'menuActivo'   => 'escenarios',
            'asignaciones' => $asignaciones
        ]);
    }

    public function ver($encodedId)
    {
        $asigModel = $this->model('AsignacionEscenario');
        $id = $this->decodeId($encodedId);

        $asignacion = $asigModel->findCompleto($id);
        if (!$asignacion || (int)$asignacion['id_estudiante'] !== Auth::id()) {
            $_SESSION['error'] = 'Asignación no encontrada';
            $this->redirect('escenario');
        }

        $this->render('escenario/ver', [
            'titulo'     => $asignacion['titulo'],
            'menuActivo' => 'escenarios',
            'asignacion' => $asignacion,
            'encodedId'  => $encodedId
        ]);
    }

    public function entregar($encodedId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('escenario');
        }

        $asigModel = $this->model('AsignacionEscenario');
        $id = $this->decodeId($encodedId);

        $asignacion = $asigModel->findCompleto($id);
        if (!$asignacion || (int)$asignacion['id_estudiante'] !== Auth::id()) {
            $_SESSION['error'] = 'Asignación no encontrada';
            $this->redirect('escenario');
        }

        if (!empty($asignacion['entrega'])) {
            $_SESSION['error'] = 'Ya entregaste este escenario. Espera la evaluación del docente.';
            $this->redirect('escenario/ver/' . $encodedId);
        }

        $contenido = trim($_POST['contenido'] ?? '');
        if (empty($contenido)) {
            $_SESSION['error'] = 'La respuesta no puede estar vacía';
            $this->redirect('escenario/ver/' . $encodedId);
        }
        if (strlen($contenido) < 20) {
            $_SESSION['error'] = 'La respuesta es muy corta. Desarrolla tu solución con al menos 20 caracteres.';
            $this->redirect('escenario/ver/' . $encodedId);
        }

        $archivoRuta = null;
        $archivoNombre = null;

        if (!empty($_FILES['archivo_adjunto']['name']) && $_FILES['archivo_adjunto']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Para archivos adjuntos de entregas, aceptamos cualquier tipo (PDF, DOCX, IMG, etc)
            $file = $_FILES['archivo_adjunto'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Error al subir el archivo adjunto';
                $this->redirect('escenario/ver/' . $encodedId);
            }
            if ($file['size'] > 20 * 1024 * 1024) {
                $_SESSION['error'] = 'El archivo adjunto no puede pesar más de 20 MB';
                $this->redirect('escenario/ver/' . $encodedId);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $extPermitidas = ['pdf', 'doc', 'docx', 'odt', 'txt', 'rtf', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
            if (!in_array($ext, $extPermitidas, true)) {
                $_SESSION['error'] = 'Tipo de archivo no permitido. Usa: ' . implode(', ', $extPermitidas);
                $this->redirect('escenario/ver/' . $encodedId);
            }

            $directorio = __DIR__ . '/../uploads/entregas';
            if (!is_dir($directorio)) mkdir($directorio, 0755, true);

            $nombreSeguro = uniqid('ent_' . $id . '_', true) . '.' . $ext;
            $destino = $directorio . '/' . $nombreSeguro;
            if (!move_uploaded_file($file['tmp_name'], $destino)) {
                $_SESSION['error'] = 'No se pudo guardar el archivo adjunto';
                $this->redirect('escenario/ver/' . $encodedId);
            }
            $archivoRuta = 'uploads/entregas/' . $nombreSeguro;
            $archivoNombre = $file['name'];
        }

        $sql = "INSERT INTO entrega_escenario (id_asignacion, contenido, archivo_ruta, archivo_nombre) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [$id, $contenido, $archivoRuta, $archivoNombre]);

        $sqlUpd = "UPDATE asignacion_escenario SET estado = 2 WHERE id = ?";
        $this->db->execute($sqlUpd, [$id]);

        $_SESSION['success'] = 'Entrega registrada. El docente evaluará tu respuesta.';
        $this->redirect('escenario/ver/' . $encodedId);
    }
}
