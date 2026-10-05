<?php
require_once __DIR__ . '/../core/Auth.php';

class AprendizajeController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
    }

    /**
     * Muestra los 4 niveles con su estado (desbloqueado/bloqueado) y progreso
     */
    public function index()
    {
        $nivelModel = $this->model('Nivel');
        $progresoModel = $this->model('ProgresoUnidad');

        $materiasIds = $this->materiasDelEstudiante();

        $niveles = $nivelModel->todos();
        $resumen = $progresoModel->resumenPorNivel(Auth::id(), $materiasIds);

        $porNivel = [];
        foreach ($resumen as $r) {
            $porNivel[(int)$r['nivel_id']] = $r;
        }

        $datos = [];
        foreach ($niveles as $n) {
            $r = $porNivel[(int)$n['id']] ?? ['total_unidades' => 0, 'unidades_completadas' => 0];
            $total = (int)$r['total_unidades'];
            $completadas = (int)$r['unidades_completadas'];
            $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
            $desbloqueado = $progresoModel->nivelDesbloqueado(Auth::id(), $n['id'], $materiasIds);

            $datos[] = [
                'nivel'         => $n,
                'total'         => $total,
                'completadas'   => $completadas,
                'porcentaje'    => $porcentaje,
                'desbloqueado'  => $desbloqueado
            ];
        }

        $this->render('aprendizaje/index', [
            'titulo'          => 'Centro de Aprendizaje',
            'menuActivo'      => 'aprendizaje',
            'niveles'         => $datos,
            'sinMaterias'     => empty($materiasIds)
        ]);
    }

    /**
     * Muestra las unidades de un nivel. Si esta bloqueado, redirige al index.
     */
    public function nivel($encodedId)
    {
        $nivelModel = $this->model('Nivel');
        $unidadModel = $this->model('Unidad');
        $progresoModel = $this->model('ProgresoUnidad');

        $materiasIds = $this->materiasDelEstudiante();

        $idNivel = $this->decodeId($encodedId);
        $nivel = $nivelModel->find($idNivel);
        if (!$nivel) {
            $_SESSION['error'] = 'Nivel no encontrado';
            $this->redirect('aprendizaje');
        }

        if (!$progresoModel->nivelDesbloqueado(Auth::id(), $idNivel, $materiasIds)) {
            $_SESSION['error'] = 'Este nivel aún no está desbloqueado. Completa el nivel anterior.';
            $this->redirect('aprendizaje');
        }

        $unidades = $unidadModel->listarPorNivel($idNivel, true, $materiasIds);
        $completadas = $progresoModel->unidadesCompletadasNivel(Auth::id(), $idNivel, $materiasIds);

        $this->render('aprendizaje/nivel', [
            'titulo'        => $nivel['nombre'],
            'menuActivo'    => 'aprendizaje',
            'nivel'         => $nivel,
            'unidades'      => $unidades,
            'completadas'   => $completadas,
            'encodedIdNivel'=> $encodedId
        ]);
    }

    /**
     * Muestra el contenido de una unidad
     */
    public function unidad($encodedId)
    {
        $unidadModel = $this->model('Unidad');
        $progresoModel = $this->model('ProgresoUnidad');

        $materiasIds = $this->materiasDelEstudiante();

        $idUnidad = $this->decodeId($encodedId);
        $unidad = $unidadModel->findCompleta($idUnidad);
        if (!$unidad) {
            $_SESSION['error'] = 'Unidad no encontrada';
            $this->redirect('aprendizaje');
        }

        if (!$progresoModel->nivelDesbloqueado(Auth::id(), $unidad['id_nivel'], $materiasIds)) {
            $_SESSION['error'] = 'Esta unidad pertenece a un nivel bloqueado';
            $this->redirect('aprendizaje');
        }

        if (!in_array((int)$unidad['id_materia'], $materiasIds, true)) {
            $_SESSION['error'] = 'Esta unidad no pertenece a tus materias';
            $this->redirect('aprendizaje');
        }

        $archivos = $unidadModel->archivos($idUnidad);
        $completada = $progresoModel->estaCompletada(Auth::id(), $idUnidad);

        $this->render('aprendizaje/unidad', [
            'titulo'     => $unidad['titulo'],
            'menuActivo' => 'aprendizaje',
            'unidad'     => $unidad,
            'archivos'   => $archivos,
            'completada' => $completada,
            'encodedId'  => $encodedId
        ]);
    }

    /**
     * Marca / desmarca una unidad como completada
     */
    public function completar($encodedId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('aprendizaje');
        }

        $unidadModel = $this->model('Unidad');
        $progresoModel = $this->model('ProgresoUnidad');

        $materiasIds = $this->materiasDelEstudiante();

        $idUnidad = $this->decodeId($encodedId);
        $unidad = $unidadModel->findCompleta($idUnidad);
        if (!$unidad) {
            $_SESSION['error'] = 'Unidad no encontrada';
            $this->redirect('aprendizaje');
        }

        if (!in_array((int)$unidad['id_materia'], $materiasIds, true)) {
            $_SESSION['error'] = 'Esta unidad no pertenece a tus materias';
            $this->redirect('aprendizaje');
        }

        $accion = $_POST['accion'] ?? 'completar';
        if ($accion === 'desmarcar') {
            $progresoModel->desmarcar(Auth::id(), $idUnidad);
            $_SESSION['success'] = 'Unidad desmarcada';
        } else {
            $progresoModel->marcar(Auth::id(), $idUnidad);
            $_SESSION['success'] = '¡Unidad completada!';
        }

        $this->redirect('aprendizaje/unidad/' . $encodedId);
    }

    /**
     * Devuelve los ids de las materias de la(s) seccion(es) del estudiante.
     * Retorna array vacio si no tiene seccion/materias asignadas.
     */
    private function materiasDelEstudiante()
    {
        $seccionModel = $this->model('Seccion');
        $materias = $seccionModel->materiasDeEstudiante(Auth::id());
        return array_map(fn($m) => (int)$m['id'], $materias);
    }
}
