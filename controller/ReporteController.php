<?php
require_once __DIR__ . '/../core/Auth.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    /**
     * Reporte de desempeno del estudiante (PDF).
     */
    public function miReporte()
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
        $idEstudiante = Auth::id();

        $progresoModel = $this->model('ProgresoUnidad');
        $asigModel = $this->model('AsignacionEscenario');
        $usuarioModel = $this->model('Usuario');
        $seccionModel = $this->model('Seccion');
        $db = \Database::getInstance();

        $estudiante = $usuarioModel->find($idEstudiante);
        $materiasIds = array_map(fn($m) => (int)$m['id'], $seccionModel->materiasDeEstudiante($idEstudiante));
        $resumenNiveles = $progresoModel->resumenPorNivel($idEstudiante, $materiasIds);
        $asignaciones = $asigModel->listarPorEstudiante($idEstudiante);

        $sql = "SELECT AVG(mejor) AS promedio FROM (
                    SELECT MAX(calificacion) AS mejor
                    FROM intento_dinamica
                    WHERE id_estudiante = ? AND fecha_fin IS NOT NULL
                    GROUP BY id_dinamica
                 ) t";
        $row = $db->fetch($sql, [$idEstudiante]);
        $promedioDinamicas = $row['promedio'] !== null ? round((float)$row['promedio'], 1) : null;

        $sql2 = "SELECT AVG(ev.calificacion) AS promedio
                 FROM evaluacion_escenario ev
                 INNER JOIN entrega_escenario en ON en.id = ev.id_entrega
                 INNER JOIN asignacion_escenario a ON a.id = en.id_asignacion
                 WHERE a.id_estudiante = ?";
        $row2 = $db->fetch($sql2, [$idEstudiante]);
        $promedioEscenarios = $row2['promedio'] !== null ? round((float)$row2['promedio'], 1) : null;

        $html = $this->renderToString('reporte/estudiante_pdf', [
            'estudiante'         => $estudiante,
            'resumenNiveles'     => $resumenNiveles,
            'asignaciones'       => $asignaciones,
            'promedioDinamicas'  => $promedioDinamicas,
            'promedioEscenarios' => $promedioEscenarios,
            'fechaGeneracion'    => date('d/m/Y H:i')
        ]);

        $this->generarPDF($html, 'reporte_' . preg_replace('/[^a-z0-9]/i', '_', $estudiante['nombre']) . '_' . date('Ymd') . '.pdf');
    }

    /**
     * Reporte del docente: desempeno agregado de su seccion (PDF).
     */
    public function reporteSeccion($encodedId)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $seccionModel = $this->model('Seccion');
        $db = \Database::getInstance();

        $idSeccion = $this->decodeId($encodedId);
        $idDocente = Auth::id();

        $seccion = $seccionModel->findCompleto($idSeccion);
        $misSecciones = array_map(fn($s) => (int)$s['id'], $seccionModel->seccionesPorDocente($idDocente));
        if (!$seccion || !in_array((int)$idSeccion, $misSecciones, true)) {
            $_SESSION['error'] = 'Sección no encontrada';
            $this->redirect('seguimiento');
        }

        $sql = "SELECT u.id, u.nombre, u.correo,
                       (SELECT COUNT(*) FROM progreso_unidad pu
                        INNER JOIN unidad un ON un.id = pu.id_unidad
                        WHERE pu.id_estudiante = u.id AND un.estado = 1) AS unidades_completadas,
                       (SELECT COUNT(DISTINCT i.id_dinamica) FROM intento_dinamica i
                        WHERE i.id_estudiante = u.id AND i.fecha_fin IS NOT NULL) AS dinamicas_completadas,
                       (SELECT AVG(i.calificacion) FROM intento_dinamica i
                        WHERE i.id_estudiante = u.id AND i.fecha_fin IS NOT NULL) AS promedio_dinamicas,
                       (SELECT COUNT(*) FROM asignacion_escenario a
                        WHERE a.id_estudiante = u.id AND a.id_seccion = s.id) AS escenarios_asignados,
                       (SELECT COUNT(*) FROM asignacion_escenario a
                        INNER JOIN entrega_escenario en ON en.id_asignacion = a.id
                        WHERE a.id_estudiante = u.id AND a.id_seccion = s.id) AS escenarios_entregados,
                       (SELECT AVG(ev.calificacion) FROM evaluacion_escenario ev
                        INNER JOIN entrega_escenario en ON en.id = ev.id_entrega
                        INNER JOIN asignacion_escenario a ON a.id = en.id_asignacion
                        WHERE a.id_estudiante = u.id AND a.id_seccion = s.id) AS promedio_escenarios
                FROM estudiante_seccion es
                INNER JOIN usuario u ON u.id = es.id_estudiante
                CROSS JOIN seccion s
                WHERE es.id_seccion = s.id AND s.id = ? AND u.estado = 1
                ORDER BY u.nombre";
        $estudiantes = $db->fetchAll($sql, [$idSeccion]);

        $html = $this->renderToString('reporte/seccion_pdf', [
            'seccion'        => $seccion,
            'estudiantes'    => $estudiantes,
            'docente'        => \Auth::nombre(),
            'fechaGeneracion'=> date('d/m/Y H:i')
        ]);

        $this->generarPDF($html, 'reporte_seccion_' . preg_replace('/[^a-z0-9]/i', '_', $seccion['nombre']) . '_' . date('Ymd') . '.pdf');
    }

    /**
     * Renderiza una vista SIN layout a un string
     */
    private function renderToString($view, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../view/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("Vista no encontrada: $view");
        }
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }

    /**
     * Genera un PDF con DomPDF y lo envia al navegador (se muestra inline)
     */
    private function generarPDF($html, $filename)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('chroot', __DIR__ . '/..');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        if (ob_get_level()) ob_end_clean();

        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }
}
