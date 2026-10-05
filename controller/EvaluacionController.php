<?php
require_once __DIR__ . '/../core/Auth.php';

class EvaluacionController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_DOCENTE);
    }

    public function index()
    {
        $db = \Database::getInstance();
        $idDocente = Auth::id();

        $filtro = $_GET['filtro'] ?? 'pendientes';

        $sqlBase = "SELECT a.id, a.fecha_asignacion, a.estado,
                           e.titulo AS escenario, n.nombre AS nivel,
                           u.nombre AS estudiante, u.correo,
                           en.id AS id_entrega, en.fecha_entrega, en.contenido,
                           ev.calificacion, ev.retroalimentacion, ev.fecha_evaluacion
                    FROM asignacion_escenario a
                    INNER JOIN escenario e ON e.id = a.id_escenario
                    INNER JOIN nivel n ON n.id = e.id_nivel
                    INNER JOIN usuario u ON u.id = a.id_estudiante
                    LEFT JOIN entrega_escenario en ON en.id_asignacion = a.id
                    LEFT JOIN evaluacion_escenario ev ON ev.id_entrega = en.id
                    WHERE a.id_docente = ?";

        if ($filtro === 'evaluados') {
            $sqlBase .= " AND a.estado = 3";
        } elseif ($filtro === 'entregados') {
            $sqlBase .= " AND a.estado IN (2, 3)";
        } else {
            $sqlBase .= " AND a.estado IN (2, 3)";
        }

        $sqlBase .= " ORDER BY en.fecha_entrega DESC, a.fecha_asignacion DESC";
        $entregas = $db->fetchAll($sqlBase, [$idDocente]);

        $this->render('evaluacion/index', [
            'titulo'     => 'Evaluar Entregas',
            'menuActivo' => 'evaluaciones',
            'entregas'   => $entregas,
            'filtro'     => $filtro
        ]);
    }

    public function evaluar($encodedId)
    {
        $db = \Database::getInstance();
        $idDocente = Auth::id();
        $id = $this->decodeId($encodedId);

        $sql = "SELECT a.*, e.titulo, e.contexto, e.problema, e.entregables_esperados,
                       e.id_nivel, n.nombre AS nivel,
                       u.nombre AS estudiante, u.correo,
                       en.id AS id_entrega, en.contenido, en.archivo_ruta, en.archivo_nombre,
                       en.fecha_entrega,
                       ev.calificacion, ev.retroalimentacion
                FROM asignacion_escenario a
                INNER JOIN escenario e ON e.id = a.id_escenario
                INNER JOIN nivel n ON n.id = e.id_nivel
                INNER JOIN usuario u ON u.id = a.id_estudiante
                LEFT JOIN entrega_escenario en ON en.id_asignacion = a.id
                LEFT JOIN evaluacion_escenario ev ON ev.id_entrega = en.id
                WHERE a.id = ? AND a.id_docente = ?";
        $asignacion = $db->fetch($sql, [$id, $idDocente]);

        if (!$asignacion) {
            $_SESSION['error'] = 'Asignación no encontrada';
            $this->redirect('evaluacion');
        }
        if (!$asignacion['id_entrega']) {
            $_SESSION['error'] = 'El estudiante aún no ha entregado este escenario';
            $this->redirect('evaluacion');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $calificacion = (float)($_POST['calificacion'] ?? -1);
            $retroalimentacion = trim($_POST['retroalimentacion'] ?? '');

            $errores = [];
            if ($calificacion < 0 || $calificacion > 100) {
                $errores[] = 'La calificación debe estar entre 0 y 100';
            }
            if (empty($retroalimentacion)) {
                $errores[] = 'La retroalimentación es obligatoria';
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('evaluacion/evaluar/' . $encodedId);
            }

            $sqlCheck = "SELECT id FROM evaluacion_escenario WHERE id_entrega = ?";
            $existe = $db->fetch($sqlCheck, [$asignacion['id_entrega']]);

            if ($existe) {
                $db->execute("UPDATE evaluacion_escenario
                              SET calificacion = ?, retroalimentacion = ?, fecha_evaluacion = NOW()
                              WHERE id_entrega = ?",
                    [$calificacion, $retroalimentacion, $asignacion['id_entrega']]);
            } else {
                $db->execute("INSERT INTO evaluacion_escenario (id_entrega, id_docente, calificacion, retroalimentacion)
                              VALUES (?, ?, ?, ?)",
                    [$asignacion['id_entrega'], $idDocente, $calificacion, $retroalimentacion]);
            }

            $db->execute("UPDATE asignacion_escenario SET estado = 3 WHERE id = ?", [$id]);

            $_SESSION['success'] = 'Evaluación registrada';
            $this->redirect('evaluacion');
        }

        $this->render('evaluacion/evaluar', [
            'titulo'      => 'Evaluar entrega',
            'menuActivo'  => 'evaluaciones',
            'asignacion'  => $asignacion,
            'encodedId'   => $encodedId
        ]);
    }
}
