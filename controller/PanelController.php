<?php
require_once __DIR__ . '/../core/Auth.php';

class PanelController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
    }

    public function index()
    {
        $idEstudiante = Auth::id();

        $progresoModel = $this->model('ProgresoUnidad');
        $nivelModel = $this->model('Nivel');
        $asigModel = $this->model('AsignacionEscenario');
        $seccionModel = $this->model('Seccion');
        $db = \Database::getInstance();

        $materiasIds = array_map(fn($m) => (int)$m['id'], $seccionModel->materiasDeEstudiante($idEstudiante));

        // Progreso por nivel
        $resumenNiveles = $progresoModel->resumenPorNivel($idEstudiante, $materiasIds);
        $porNivel = [];
        foreach ($resumenNiveles as $r) {
            $r['porcentaje'] = $r['total_unidades'] > 0
                ? round(($r['unidades_completadas'] / $r['total_unidades']) * 100)
                : 0;
            $r['desbloqueado'] = $progresoModel->nivelDesbloqueado($idEstudiante, $r['nivel_id'], $materiasIds);
            $porNivel[(int)$r['nivel_id']] = $r;
        }
        $niveles = $nivelModel->todos();

        // Escenarios
        $asignaciones = $asigModel->listarPorEstudiante($idEstudiante);
        $totalAsignados = count($asignaciones);
        $totalEntregados = count(array_filter($asignaciones, fn($a) => in_array((int)$a['estado'], [2, 3], true)));
        $totalEvaluados = count(array_filter($asignaciones, fn($a) => (int)$a['estado'] === 3));
        $promedioCalif = 0;
        $calificaciones = array_filter(array_map(fn($a) => $a['calificacion'], $asignaciones));
        if (!empty($calificaciones)) {
            $promedioCalif = array_sum($calificaciones) / count($calificaciones);
        }

        // Dinamicas: mejor calificacion por dinamica
        $sqlDin = "SELECT d.id, d.titulo, n.nombre AS nivel,
                          MAX(i.calificacion) AS mejor, COUNT(i.id) AS intentos,
                          MAX(i.fecha_fin) AS ultimo
                   FROM dinamica d
                   INNER JOIN nivel n ON n.id = d.id_nivel
                   LEFT JOIN intento_dinamica i ON i.id_dinamica = d.id AND i.id_estudiante = ? AND i.fecha_fin IS NOT NULL
                   WHERE d.estado = 1
                   GROUP BY d.id, d.titulo, n.nombre
                   ORDER BY n.numero, d.id";
        $dinamicasRaw = $db->fetchAll($sqlDin, [$idEstudiante]);

        $intentos = [];
        foreach ($dinamicasRaw as $d) {
            if ($d['mejor'] !== null) {
                $intentos[] = $d;
            }
        }

        return $this->render('panel/index', [
            'titulo'           => 'Mi Progreso',
            'menuActivo'       => 'panel',
            'niveles'          => $niveles,
            'porNivel'         => $porNivel,
            'asignaciones'     => $asignaciones,
            'totalAsignados'   => $totalAsignados,
            'totalEntregados'  => $totalEntregados,
            'totalEvaluados'   => $totalEvaluados,
            'promedioCalif'    => $promedioCalif,
            'intentos'         => $intentos
        ]);
    }
}
