<?php
require_once __DIR__ . '/../core/Auth.php';

class SeguimientoController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_DOCENTE);
    }

    public function index()
    {
        $seccionModel = $this->model('Seccion');
        $db = \Database::getInstance();

        $idDocente = Auth::id();
        $secciones = $seccionModel->seccionesPorDocente($idDocente);

        // Por cada seccion, listar estudiantes con resumen de progreso
        $data = [];
        foreach ($secciones as $s) {
            $sql = "SELECT u.id, u.nombre, u.correo,
                           (SELECT COUNT(*) FROM progreso_unidad pu
                            INNER JOIN unidad un ON un.id = pu.id_unidad
                            WHERE pu.id_estudiante = u.id AND un.estado = 1) AS unidades_completadas,
                           (SELECT COUNT(DISTINCT i.id_dinamica) FROM intento_dinamica i
                            WHERE i.id_estudiante = u.id AND i.fecha_fin IS NOT NULL) AS dinamicas_completadas,
                           (SELECT AVG(i.calificacion) FROM intento_dinamica i
                            WHERE i.id_estudiante = u.id AND i.fecha_fin IS NOT NULL) AS promedio_dinamicas,
                           (SELECT COUNT(*) FROM asignacion_escenario a
                            WHERE a.id_estudiante = u.id AND a.id_seccion = es.id_seccion) AS escenarios_asignados,
                           (SELECT COUNT(*) FROM asignacion_escenario a
                            INNER JOIN entrega_escenario en ON en.id_asignacion = a.id
                            WHERE a.id_estudiante = u.id AND a.id_seccion = es.id_seccion) AS escenarios_entregados,
                           (SELECT AVG(ev.calificacion) FROM evaluacion_escenario ev
                            INNER JOIN entrega_escenario en ON en.id = ev.id_entrega
                            INNER JOIN asignacion_escenario a ON a.id = en.id_asignacion
                            WHERE a.id_estudiante = u.id AND a.id_seccion = es.id_seccion) AS promedio_escenarios
                    FROM estudiante_seccion es
                    INNER JOIN usuario u ON u.id = es.id_estudiante
                    WHERE es.id_seccion = ? AND u.estado = 1
                    ORDER BY u.nombre";
            $estudiantes = $db->fetchAll($sql, [$s['id']]);

            $data[] = [
                'seccion'     => $s,
                'estudiantes' => $estudiantes
            ];
        }

        $this->render('seguimiento/index', [
            'titulo'     => 'Seguimiento de Estudiantes',
            'menuActivo' => 'seguimiento',
            'secciones'  => $data
        ]);
    }
}
