<?php
require_once __DIR__ . '/../core/Auth.php';

class AsignacionController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_DOCENTE);
    }

    public function index()
    {
        $db = \Database::getInstance();
        $idDocente = Auth::id();

        // Listar todas las asignaciones hechas por este docente
        $sql = "SELECT a.*, e.titulo AS escenario, n.nombre AS nivel,
                       u.nombre AS estudiante, p.nombre AS periodo,
                       en.id AS id_entrega, ev.calificacion
                FROM asignacion_escenario a
                INNER JOIN escenario e ON e.id = a.id_escenario
                INNER JOIN nivel n ON n.id = e.id_nivel
                INNER JOIN usuario u ON u.id = a.id_estudiante
                INNER JOIN seccion s ON s.id = a.id_seccion
                INNER JOIN periodo_academico p ON p.id = s.id_periodo
                LEFT JOIN entrega_escenario en ON en.id_asignacion = a.id
                LEFT JOIN evaluacion_escenario ev ON ev.id_entrega = en.id
                WHERE a.id_docente = ?
                ORDER BY a.estado ASC, a.fecha_asignacion DESC";
        $asignaciones = $db->fetchAll($sql, [$idDocente]);

        $this->render('asignacion/index', [
            'titulo'       => 'Asignar Escenarios',
            'menuActivo'   => 'asignaciones',
            'asignaciones' => $asignaciones
        ]);
    }

    public function form()
    {
        $seccionModel = $this->model('Seccion');
        $escenarioModel = $this->model('Escenario');
        $db = \Database::getInstance();
        $idDocente = Auth::id();

        $secciones = $seccionModel->seccionesPorDocente($idDocente);
        $escenarios = $escenarioModel->listarTodos();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idSeccion = (int)($_POST['id_seccion'] ?? 0);
            $idEscenario = (int)($_POST['id_escenario'] ?? 0);
            $estudiantes = $_POST['estudiantes'] ?? [];
            $fechaLimite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;
            $observaciones = trim($_POST['observaciones_docente'] ?? '');

            $errores = [];

            if ($idSeccion <= 0) $errores[] = 'Debe seleccionar una sección';
            if ($idEscenario <= 0) $errores[] = 'Debe seleccionar un escenario';
            if (empty($estudiantes) || !is_array($estudiantes)) $errores[] = 'Debe seleccionar al menos un estudiante';

            // Verificar que la sección pertenezca al docente
            $seccionesDelDocente = array_map(fn($s) => (int)$s['id'], $secciones);
            if (!in_array($idSeccion, $seccionesDelDocente, true)) {
                $errores[] = 'La sección seleccionada no te pertenece';
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('asignacion/form');
            }

            $ok = 0;
            foreach ($estudiantes as $idEst) {
                $idEst = (int)$idEst;
                if ($idEst <= 0) continue;

                $sql = "INSERT INTO asignacion_escenario
                            (id_escenario, id_estudiante, id_docente, id_seccion, fecha_limite, observaciones_docente, estado)
                        VALUES (?, ?, ?, ?, ?, ?, 1)
                        ON DUPLICATE KEY UPDATE fecha_limite = VALUES(fecha_limite),
                                                observaciones_docente = VALUES(observaciones_docente),
                                                estado = IF(estado = 3, estado, 1)";
                $db->execute($sql, [$idEscenario, $idEst, $idDocente, $idSeccion, $fechaLimite, $observaciones]);
                $ok++;
            }

            $_SESSION['success'] = "Se asignó el escenario a {$ok} estudiante(s)";
            $this->redirect('asignacion');
        }

        $this->render('asignacion/form', [
            'titulo'     => 'Nueva Asignación',
            'menuActivo' => 'asignaciones',
            'secciones'  => $secciones,
            'escenarios' => $escenarios
        ]);
    }

    public function eliminar($encodedId)
    {
        $db = \Database::getInstance();
        $id = $this->decodeId($encodedId);
        $idDocente = Auth::id();

        $asig = $db->fetch("SELECT a.*, en.id AS id_entrega
                            FROM asignacion_escenario a
                            LEFT JOIN entrega_escenario en ON en.id_asignacion = a.id
                            WHERE a.id = ?", [$id]);

        if (!$asig || (int)$asig['id_docente'] !== (int)$idDocente) {
            $_SESSION['error'] = 'Asignación no encontrada';
            $this->redirect('asignacion');
        }

        if ($asig['id_entrega']) {
            $_SESSION['error'] = 'No se puede eliminar: el estudiante ya entregó';
            $this->redirect('asignacion');
        }

        $db->execute("DELETE FROM asignacion_escenario WHERE id = ?", [$id]);
        $_SESSION['success'] = 'Asignación eliminada';
        $this->redirect('asignacion');
    }

    /**
     * AJAX: lista estudiantes de una seccion del docente autenticado
     */
    public function estudiantesSeccionAjax()
    {
        ob_start();
        try {
            $idSeccion = (int)($_GET['seccion'] ?? 0);
            $idDocente = Auth::id();

            if ($idSeccion <= 0) {
                throw new Exception('Sección inválida');
            }

            $seccionModel = $this->model('Seccion');
            $misSecciones = $seccionModel->seccionesPorDocente($idDocente);
            $idsValidos = array_map(fn($s) => (int)$s['id'], $misSecciones);

            if (!in_array($idSeccion, $idsValidos, true)) {
                throw new Exception('La sección no te pertenece');
            }

            $estudiantes = $seccionModel->estudiantesAsignados($idSeccion);
            $response = ['success' => true, 'estudiantes' => $estudiantes];
        } catch (Exception $e) {
            $response = ['success' => false, 'error' => $e->getMessage()];
        } catch (Throwable $e) {
            $response = ['success' => false, 'error' => 'Error inesperado'];
        }

        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
