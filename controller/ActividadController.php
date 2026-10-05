<?php
require_once __DIR__ . '/../core/Auth.php';

class ActividadController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    // =====================================================================
    // INDEX (rol segun usuario)
    // =====================================================================

    public function index()
    {
        if (\Auth::isEstudiante()) {
            return $this->indexEstudiante();
        }
        return $this->indexDocente();
    }

    private function indexDocente()
    {
        $this->requireRole(Auth::ROL_DOCENTE);

        $dmModel = $this->model('DocenteMateria');
        $actModel = $this->model('Actividad');

        $materias = $dmModel->materiasDeDocente(Auth::id());
        if (empty($materias)) {
            $this->render('actividad/index', [
                'titulo'     => 'Actividades',
                'menuActivo' => 'actividades',
                'materias'   => [],
                'materiaSel' => null,
                'actividades'=> [],
                'esDocente'  => true
            ]);
            return;
        }

        $materiaSel = (int)($_GET['materia'] ?? $materias[0]['id']);
        $existe = false;
        foreach ($materias as $m) {
            if ((int)$m['id'] === $materiaSel) { $existe = true; break; }
        }
        if (!$existe) $materiaSel = (int)$materias[0]['id'];

        $actividades = $actModel->listarPorMateria($materiaSel);

        $this->render('actividad/index', [
            'titulo'     => 'Actividades',
            'menuActivo' => 'actividades',
            'materias'   => $materias,
            'materiaSel' => $materiaSel,
            'actividades'=> $actividades,
            'esDocente'  => true
        ]);
    }

    private function indexEstudiante()
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);

        $seccionModel = $this->model('Seccion');
        $actModel = $this->model('Actividad');

        $materias = $seccionModel->materiasDeEstudiante(Auth::id());
        if (empty($materias)) {
            $this->render('actividad/index', [
                'titulo'     => 'Actividades',
                'menuActivo' => 'actividades',
                'materias'   => [],
                'materiaSel' => null,
                'actividades'=> [],
                'esDocente'  => false
            ]);
            return;
        }

        $materiaSel = (int)($_GET['materia'] ?? $materias[0]['id']);
        $existe = false;
        foreach ($materias as $m) {
            if ((int)$m['id'] === $materiaSel) { $existe = true; break; }
        }
        if (!$existe) $materiaSel = (int)$materias[0]['id'];

        $actividades = $actModel->listarParaEstudiante(Auth::id(), $materiaSel);

        $this->render('actividad/index', [
            'titulo'     => 'Actividades',
            'menuActivo' => 'actividades',
            'materias'   => $materias,
            'materiaSel' => $materiaSel,
            'actividades'=> $actividades,
            'esDocente'  => false
        ]);
    }

    // =====================================================================
    // CRUD ACTIVIDAD (docente)
    // =====================================================================

    public function form($encodedId = null)
    {
        $this->requireRole(Auth::ROL_DOCENTE);

        $actModel = $this->model('Actividad');
        $dmModel = $this->model('DocenteMateria');
        $seccionModel = $this->model('Seccion');
        $nivelModel = $this->model('Nivel');
        $unidadModel = $this->model('Unidad');

        $materias = $dmModel->materiasDeDocente(Auth::id());
        if (empty($materias)) {
            $_SESSION['error'] = 'No tienes materias asignadas. Contacta al administrador.';
            $this->redirect('actividad');
        }

        $actividad = null;
        $seccionesAsignadas = [];

        if ($encodedId) {
            $id = $this->decodeId($encodedId);
            $actividad = $actModel->findCompleta($id);
            if (!$actividad) {
                $_SESSION['error'] = 'Actividad no encontrada';
                $this->redirect('actividad');
            }
            $seccionesAsignadas = array_map(fn($s) => (int)$s['id'], $actModel->secciones($id));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idMateria = (int)($_POST['id_materia'] ?? 0);
            $idUnidad = (int)($_POST['id_unidad'] ?? 0);
            $idNivel = !empty($_POST['id_nivel']) ? (int)$_POST['id_nivel'] : null;
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $estado = (int)($_POST['estado'] ?? 1);

            $idsSecciones = $_POST['secciones'] ?? [];
            if (!is_array($idsSecciones)) $idsSecciones = [];
            $idsSecciones = array_values(array_unique(array_map('intval', array_filter($idsSecciones, 'is_numeric'))));

            $errores = [];
            if ($titulo === '') {
                $errores[] = 'El título es obligatorio';
            } elseif (strlen($titulo) > 150) {
                $errores[] = 'El título no puede exceder 150 caracteres';
            }

            $materiaValida = false;
            foreach ($materias as $m) {
                if ((int)$m['id'] === $idMateria) { $materiaValida = true; break; }
            }
            if (!$materiaValida) $errores[] = 'La materia seleccionada no es válida';

            // La unidad debe existir y pertenecer a la materia (las unidades las crea el administrador)
            $unidadesMateria = $unidadModel->listarPorMateria($idMateria, false);
            $unidadValida = false;
            foreach ($unidadesMateria as $u) {
                if ((int)$u['id'] === $idUnidad) { $unidadValida = true; break; }
            }
            if (!$unidadValida) {
                $errores[] = 'Debe seleccionar una unidad de la materia (las unidades las crea el administrador)';
            }

            if (!in_array($estado, [1, 2], true)) $errores[] = 'Estado inválido';

            // Validar que las secciones pertenezcan al docente y tengan la materia
            $validas = $seccionModel->seccionesDelDocenteConMateria(Auth::id(), $idMateria);
            $idsValidos = array_map(fn($s) => (int)$s['id'], $validas);
            foreach ($idsSecciones as $idS) {
                if (!in_array($idS, $idsValidos, true)) {
                    $errores[] = 'Una sección seleccionada no es válida para la materia';
                    break;
                }
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('actividad/form' . ($encodedId ? '/' . $encodedId : ''));
            }

            $data = [
                'id_materia'  => $idMateria,
                'id_unidad'   => $idUnidad,
                'titulo'      => $titulo,
                'descripcion' => $descripcion ?: null,
                'id_nivel'    => $idNivel,
                'estado'      => $estado,
            ];

            if ($actividad) {
                $actModel->update($actividad['id'], $data);
                $idActividad = $actividad['id'];
            } else {
                $idActividad = $actModel->create($data);
            }

            $actModel->asignarSecciones($idActividad, $idsSecciones);
            $_SESSION['success'] = 'Actividad guardada exitosamente';
            $this->redirect('actividad');
        }

        $this->render('actividad/form', [
            'titulo'            => ($actividad ? 'Editar' : 'Nueva') . ' Actividad',
            'menuActivo'        => 'actividades',
            'actividad'         => $actividad,
            'materias'          => $materias,
            'niveles'           => $nivelModel->todos(),
            'unidades'          => $unidadModel->listarPorMateria($actividad ? (int)$actividad['id_materia'] : 0, false),
            'secciones'         => $seccionModel->seccionesDelDocenteConMateria(Auth::id(), $actividad ? (int)$actividad['id_materia'] : 0),
            'seccionesAsignadas'=> $seccionesAsignadas,
            'encodedId'         => $encodedId
        ]);
    }

    public function eliminar($encodedId)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $actModel = $this->model('Actividad');

        $id = $this->decodeId($encodedId);
        $actividad = $actModel->findCompleta($id);
        if (!$actividad) {
            $_SESSION['error'] = 'Actividad no encontrada';
            $this->redirect('actividad');
        }

        $actModel->delete($id);
        $_SESSION['success'] = 'Actividad eliminada exitosamente';
        $this->redirect('actividad');
    }

    /**
     * JSON: secciones del docente que tienen una materia (para el formulario).
     */
    public function seccionesJson()
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $seccionModel = $this->model('Seccion');

        $idMateria = (int)($_GET['materia'] ?? 0);
        $response = ['success' => true, 'secciones' => []];
        if ($idMateria > 0) {
            $response['secciones'] = $seccionModel->seccionesDelDocenteConMateria(Auth::id(), $idMateria);
        }

        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * JSON: unidades (publicadas) de una materia (para el formulario de actividad).
     */
    public function unidadesJson()
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $unidadModel = $this->model('Unidad');

        $idMateria = (int)($_GET['materia'] ?? 0);
        $response = ['success' => true, 'unidades' => []];
        if ($idMateria > 0) {
            $response['unidades'] = $unidadModel->listarPorMateria($idMateria, true);
        }

        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =====================================================================
    // PARTES (docente)
    // =====================================================================

    public function partes($encodedId)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $actModel = $this->model('Actividad');

        $id = $this->decodeId($encodedId);
        $actividad = $actModel->findCompleta($id);
        if (!$actividad) {
            $_SESSION['error'] = 'Actividad no encontrada';
            $this->redirect('actividad');
        }

        $this->render('actividad/partes', [
            'titulo'     => 'Partes de la Actividad',
            'menuActivo' => 'actividades',
            'actividad'  => $actividad,
            'partes'     => $actModel->partes($id),
            'encodedId'  => $encodedId
        ]);
    }

    public function parteForm($encodedActividad, $encodedParte = null)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $actModel = $this->model('Actividad');

        $idActividad = $this->decodeId($encodedActividad);
        $actividad = $actModel->findCompleta($idActividad);
        if (!$actividad) {
            $_SESSION['error'] = 'Actividad no encontrada';
            $this->redirect('actividad');
        }

        $parte = null;
        $preguntas = [];
        if ($encodedParte) {
            $idParte = $this->decodeId($encodedParte);
            $parte = $actModel->parteFind($idParte);
            if (!$parte || (int)$parte['id_actividad'] !== (int)$idActividad) {
                $_SESSION['error'] = 'Parte no encontrada';
                $this->redirect('actividad/partes/' . $encodedActividad);
            }
            $preguntas = $actModel->preguntasDeParte($idParte);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $contenido = $_POST['contenido_html'] ?? null;
            $tiempo = !empty($_POST['tiempo_limite_min']) ? (int)$_POST['tiempo_limite_min'] : null;
            $orden = (int)($_POST['orden'] ?? 1);
            $estado = (int)($_POST['estado'] ?? 1);

            $errores = [];
            if ($titulo === '') {
                $errores[] = 'El título de la parte es obligatorio';
            } elseif (strlen($titulo) > 150) {
                $errores[] = 'El título no puede exceder 150 caracteres';
            }
            if ($tiempo !== null && $tiempo < 1) $tiempo = null;
            if (!in_array($estado, [1, 2], true)) $estado = 1;

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('actividad/parteForm/' . $encodedActividad . ($encodedParte ? '/' . $encodedParte : ''));
            }

            if ($contenido !== null && $contenido !== '') {
                $contenido = $this->sanitizarHTML($contenido);
            }

            $actModel->parteGuardar($idActividad, [
                'titulo'           => $titulo,
                'contenido_html'   => $contenido,
                'tiempo_limite_min'=> $tiempo,
                'orden'            => $orden,
                'estado'           => $estado,
            ], $parte ? (int)$parte['id'] : null);

            $_SESSION['success'] = 'Parte guardada exitosamente';
            $this->redirect('actividad/partes/' . $encodedActividad);
        }

        $this->render('actividad/parte_form', [
            'titulo'      => ($parte ? 'Editar' : 'Nueva') . ' Parte',
            'menuActivo'  => 'actividades',
            'actividad'   => $actividad,
            'parte'       => $parte,
            'preguntas'   => $preguntas,
            'siguienteOrden' => $actModel->siguienteOrdenParte($idActividad),
            'encodedActividad' => $encodedActividad,
            'encodedParte'     => $encodedParte
        ]);
    }

    public function parteEliminar($encodedActividad, $encodedParte)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $actModel = $this->model('Actividad');

        $idParte = $this->decodeId($encodedParte);
        $parte = $actModel->parteFind($idParte);
        if ($parte) $actModel->parteEliminar($idParte);

        $_SESSION['success'] = 'Parte eliminada';
        $this->redirect('actividad/partes/' . $encodedActividad);
    }

    // =====================================================================
    // PREGUNTAS (docente)
    // =====================================================================

    public function preguntaForm($encodedParte, $encodedPregunta = null)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $actModel = $this->model('Actividad');

        $idParte = $this->decodeId($encodedParte);
        $parte = $actModel->parteFind($idParte);
        if (!$parte) {
            $_SESSION['error'] = 'Parte no encontrada';
            $this->redirect('actividad');
        }
        $idActividad = (int)$parte['id_actividad'];
        $actividad = $actModel->findCompleta($idActividad);
        $encodedActividad = $this->encodeId($idActividad);

        $pregunta = null;
        $opciones = [];
        if ($encodedPregunta) {
            $idPregunta = $this->decodeId($encodedPregunta);
            $pregunta = $actModel->preguntaFind($idPregunta);
            if (!$pregunta || (int)$pregunta['id_parte'] !== (int)$idParte) {
                $_SESSION['error'] = 'Pregunta no encontrada';
                $this->redirect('actividad/parteForm/' . $encodedActividad . '/' . $encodedParte);
            }
            $preguntas = $actModel->preguntasDeParte($idParte);
            foreach ($preguntas as $p) {
                if ((int)$p['id'] === (int)$idPregunta) { $opciones = $p['opciones']; break; }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enunciado = trim($_POST['enunciado'] ?? '');
            $puntos = (int)($_POST['puntos'] ?? 1);
            $orden = (int)($_POST['orden'] ?? 1);

            $opciones = [];
            for ($i = 1; $i <= 4; $i++) {
                $opciones[] = [
                    'texto'            => trim($_POST['opcion_' . $i] ?? ''),
                    'correcta'         => ($_POST['correcta'] ?? '') === (string)$i,
                    'retroalimentacion'=> trim($_POST['retro_' . $i] ?? ''),
                ];
            }

            $errores = [];
            if ($enunciado === '') {
                $errores[] = 'El enunciado es obligatorio';
            }
            if ($puntos < 1) $puntos = 1;
            if ($orden < 1) $orden = 1;

            $opcionesLlenas = array_filter($opciones, fn($o) => $o['texto'] !== '');
            if (count($opcionesLlenas) < 2) {
                $errores[] = 'Debe completar al menos 2 opciones';
            }
            $correctas = array_filter($opciones, fn($o) => $o['correcta'] && $o['texto'] !== '');
            if (count($correctas) !== 1) {
                $errores[] = 'Debe marcar exactamente una opción como correcta';
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('actividad/preguntaForm/' . $encodedParte . ($encodedPregunta ? '/' . $encodedPregunta : ''));
            }

            $idPregunta = $actModel->preguntaGuardar($idParte, [
                'enunciado' => $enunciado,
                'orden'     => $orden,
                'puntos'    => $puntos,
            ], $pregunta ? (int)$pregunta['id'] : null);

            $actModel->opcionesGuardar($idPregunta, $opciones);

            $_SESSION['success'] = 'Pregunta guardada exitosamente';
            $this->redirect('actividad/parteForm/' . $encodedActividad . '/' . $encodedParte);
        }

        $this->render('actividad/pregunta_form', [
            'titulo'            => ($pregunta ? 'Editar' : 'Nueva') . ' Pregunta',
            'menuActivo'        => 'actividades',
            'actividad'         => $actividad,
            'parte'             => $parte,
            'pregunta'          => $pregunta,
            'opciones'          => $opciones,
            'siguienteOrden'    => $actModel->siguienteOrdenPregunta($idParte),
            'encodedActividad'  => $encodedActividad,
            'encodedParte'      => $encodedParte,
            'encodedPregunta'   => $encodedPregunta
        ]);
    }

    public function preguntaEliminar($encodedParte, $encodedPregunta)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $actModel = $this->model('Actividad');

        $idPregunta = $this->decodeId($encodedPregunta);
        $pregunta = $actModel->preguntaFind($idPregunta);
        if ($pregunta) {
            $idParte = (int)$pregunta['id_parte'];
            $parte = $actModel->parteFind($idParte);
            $encodedActividad = $parte ? $this->encodeId($parte['id_actividad']) : '';
            $actModel->preguntaEliminar($idPregunta);
            $_SESSION['success'] = 'Pregunta eliminada';
            $this->redirect('actividad/parteForm/' . $encodedActividad . '/' . $encodedParte);
        }
        $this->redirect('actividad');
    }

    // =====================================================================
    // ENTREGAS (docente)
    // =====================================================================

    public function entregas($encodedId)
    {
        $this->requireRole(Auth::ROL_DOCENTE);
        $actModel = $this->model('Actividad');
        $entregaModel = $this->model('ActividadEntrega');

        $id = $this->decodeId($encodedId);
        $actividad = $actModel->findCompleta($id);
        if (!$actividad) {
            $_SESSION['error'] = 'Actividad no encontrada';
            $this->redirect('actividad');
        }

        $this->render('actividad/entregas', [
            'titulo'     => 'Entregas de la Actividad',
            'menuActivo' => 'actividades',
            'actividad'  => $actividad,
            'entregas'   => $entregaModel->entregasDeActividad($id),
            'encodedId'  => $encodedId
        ]);
    }

    // =====================================================================
    // FLUJO DEL ESTUDIANTE
    // =====================================================================

    public function ver($encodedId)
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
        $actModel = $this->model('Actividad');
        $entregaModel = $this->model('ActividadEntrega');

        $id = $this->decodeId($encodedId);
        $actividad = $actModel->findCompleta($id);
        if (!$actividad || (int)$actividad['estado'] !== 1) {
            $_SESSION['error'] = 'Actividad no encontrada';
            $this->redirect('actividad');
        }
        if (!$actModel->estudianteTieneAcceso(Auth::id(), $id)) {
            $_SESSION['error'] = 'No tienes acceso a esta actividad';
            $this->redirect('actividad');
        }

        $partes = $actModel->partes($id);
        $partesActivas = array_values(array_filter($partes, fn($p) => (int)$p['estado'] === 1));
        $entrega = $entregaModel->obtener($id, Auth::id());
        $respondidas = $entregaModel->respuestas($entrega['id']);

        $parteActual = null;
        $partesCompletadas = [];
        foreach ($partesActivas as $p) {
            if (isset($respondidas[(int)$p['id']]) && $respondidas[(int)$p['id']]['respondida']) {
                $partesCompletadas[] = (int)$p['id'];
            } elseif ($parteActual === null) {
                $parteActual = $p;
            }
        }

        $completada = (int)$entrega['estado'] === 2;
        $indiceActual = -1;
        foreach ($partesActivas as $i => $p) {
            if ($parteActual !== null && (int)$p['id'] === (int)$parteActual['id']) {
                $indiceActual = $i;
                break;
            }
        }

        $this->render('actividad/estudiante_ver', [
            'titulo'            => $actividad['titulo'],
            'menuActivo'        => 'actividades',
            'actividad'         => $actividad,
            'partes'            => $partesActivas,
            'finalizada'        => $completada,
            'calificacion'      => $entrega['calificacion'],
            'totalPartes'       => count($partesActivas),
            'partesResueltas'   => count($partesCompletadas),
            'partesCompletadas' => $partesCompletadas,
            'indiceActual'      => $indiceActual,
            'encodedId'         => $encodedId
        ]);
    }

    public function evaluacion($encodedActividad, $encodedParte)
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
        $actModel = $this->model('Actividad');
        $entregaModel = $this->model('ActividadEntrega');

        $idActividad = $this->decodeId($encodedActividad);
        $idParte = $this->decodeId($encodedParte);

        $actividad = $actModel->findCompleta($idActividad);
        if (!$actividad || (int)$actividad['estado'] !== 1) {
            $_SESSION['error'] = 'Actividad no encontrada';
            $this->redirect('actividad');
        }
        if (!$actModel->estudianteTieneAcceso(Auth::id(), $idActividad)) {
            $_SESSION['error'] = 'No tienes acceso a esta actividad';
            $this->redirect('actividad');
        }

        $parte = $actModel->parteFind($idParte);
        if (!$parte || (int)$parte['id_actividad'] !== (int)$idActividad || (int)$parte['estado'] !== 1) {
            $_SESSION['error'] = 'Parte no encontrada';
            $this->redirect('actividad/ver/' . $encodedActividad);
        }

        $partes = $actModel->partes($idActividad);
        $partesActivas = array_values(array_filter($partes, fn($p) => (int)$p['estado'] === 1));
        $entrega = $entregaModel->obtener($idActividad, Auth::id());
        $respondidas = $entregaModel->respuestas($entrega['id']);

        // Verificar avance secuencial: partes anteriores deben estar respondidas
        $previoOk = true;
        foreach ($partesActivas as $p) {
            if ((int)$p['id'] === (int)$idParte) break;
            if (!isset($respondidas[(int)$p['id']]) || !$respondidas[(int)$p['id']]['respondida']) {
                $previoOk = false;
                break;
            }
        }
        if (!$previoOk) {
            $_SESSION['error'] = 'Completa primero la parte anterior';
            $this->redirect('actividad/ver/' . $encodedActividad);
        }

        // Ya respondida
        if (isset($respondidas[(int)$idParte]) && $respondidas[(int)$idParte]['respondida']) {
            $this->redirect('actividad/ver/' . $encodedActividad);
        }

        $preguntas = $actModel->preguntasDeParte($idParte);

        // Temporizador persistente: se inicia al entrar por primera vez y sigue
        // corriendo aunque el estudiante salga. Si el tiempo se agota mientras
        // estuvo fuera, la parte se cierra automaticamente con 0 puntos.
        $tiempoRestante = null;
        if ((int)$parte['tiempo_limite_min'] > 0) {
            $tiempoRestante = $entregaModel->inicioParte($entrega['id'], $idParte, (int)$parte['tiempo_limite_min']);
            if ($tiempoRestante !== null && $tiempoRestante <= 0) {
                $entregaModel->guardarRespuesta(
                    $entrega['id'],
                    $idParte,
                    0,
                    (int)$parte['tiempo_limite_min'] * 60,
                    count($partesActivas)
                );
                $_SESSION['error'] = 'El tiempo de esta parte se agotó mientras no estabas en la evaluación.';
                $this->redirect('actividad/ver/' . $encodedActividad);
            }
        }

        $this->render('actividad/evaluacion', [
            'titulo'      => 'Evaluación: ' . $parte['titulo'],
            'menuActivo'  => 'actividades',
            'actividad'   => $actividad,
            'parte'       => $parte,
            'preguntas'   => $preguntas,
            'tiempoRestante' => $tiempoRestante,
            'encodedActividad' => $encodedActividad,
            'encodedParte'     => $encodedParte
        ]);
    }

    public function entregar($encodedActividad, $encodedParte)
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('actividad');
        }

        $actModel = $this->model('Actividad');
        $entregaModel = $this->model('ActividadEntrega');

        $idActividad = $this->decodeId($encodedActividad);
        $idParte = $this->decodeId($encodedParte);

        $actividad = $actModel->findCompleta($idActividad);
        if (!$actividad || (int)$actividad['estado'] !== 1) {
            $this->redirect('actividad');
        }
        if (!$actModel->estudianteTieneAcceso(Auth::id(), $idActividad)) {
            $this->redirect('actividad');
        }

        $parte = $actModel->parteFind($idParte);
        if (!$parte || (int)$parte['id_actividad'] !== (int)$idActividad) {
            $this->redirect('actividad');
        }

        $partes = $actModel->partes($idActividad);
        $partesActivas = array_values(array_filter($partes, fn($p) => (int)$p['estado'] === 1));
        $totalPartes = count($partesActivas);
        $entrega = $entregaModel->obtener($idActividad, Auth::id());
        $respondidas = $entregaModel->respuestas($entrega['id']);

        if (isset($respondidas[(int)$idParte]) && $respondidas[(int)$idParte]['respondida']) {
            $this->redirect('actividad/ver/' . $encodedActividad);
        }

        $preguntas = $actModel->preguntasDeParte($idParte);
        $totalPuntos = 0;
        $puntosCorrectos = 0;

        $opcionesIds = [];
        foreach ($preguntas as $p) {
            $totalPuntos += (int)$p['puntos'];
            foreach ($p['opciones'] as $o) {
                $opcionesIds[(int)$o['id']] = [
                    'pregunta_id' => (int)$p['id'],
                    'correcta'    => (int)$o['es_correcta'] === 1,
                    'puntos'      => (int)$p['puntos'],
                ];
            }
        }

        $respuestas = $_POST['respuestas'] ?? [];
        if (!is_array($respuestas)) $respuestas = [];

        foreach ($respuestas as $idOpcionRaw) {
            $idOpcion = (int)$idOpcionRaw;
            if (!isset($opcionesIds[$idOpcion])) continue;
            if ($opcionesIds[$idOpcion]['correcta']) {
                $puntosCorrectos += $opcionesIds[$idOpcion]['puntos'];
            }
        }

        $puntaje = $totalPuntos > 0 ? round(($puntosCorrectos / $totalPuntos) * 100, 2) : 0;
        $tiempoUsado = (int)($_POST['tiempo_usado'] ?? 0);
        $tiempoReal = $entregaModel->tiempoUsadoParte($entrega['id'], $idParte);
        if ($tiempoReal !== null) {
            $tiempoUsado = min($tiempoReal, (int)$parte['tiempo_limite_min'] * 60);
        }

        $entregaModel->guardarRespuesta($entrega['id'], $idParte, $puntaje, $tiempoUsado, $totalPartes);

        $_SESSION['success'] = 'Evaluación enviada. Puntaje de la parte: ' . number_format($puntaje, 1) . '%';
        $this->redirect('actividad/ver/' . $encodedActividad);
    }

    public function resultado($encodedId)
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
        $actModel = $this->model('Actividad');
        $entregaModel = $this->model('ActividadEntrega');

        $id = $this->decodeId($encodedId);
        $actividad = $actModel->findCompleta($id);
        if (!$actividad) {
            $_SESSION['error'] = 'Actividad no encontrada';
            $this->redirect('actividad');
        }
        if (!$actModel->estudianteTieneAcceso(Auth::id(), $id)) {
            $this->redirect('actividad');
        }

        $entrega = $entregaModel->obtener($id, Auth::id());
        if ((int)$entrega['estado'] !== 2) {
            $this->redirect('actividad/ver/' . $encodedId);
        }

        $partes = $actModel->partes($id);
        $partesActivas = array_values(array_filter($partes, fn($p) => (int)$p['estado'] === 1));
        $respondidas = $entregaModel->respuestas($entrega['id']);

        $calificacion = (float)$entrega['calificacion'];
        $aprobado = $calificacion >= 60;

        $detalle = [];
        foreach ($partesActivas as $p) {
            $resp = $respondidas[(int)$p['id']] ?? null;
            $total = (int)$p['total_puntos'];
            $puntaje = $resp ? (float)$resp['puntaje'] : 0;
            $detalle[] = [
                'orden'           => (int)$p['orden'],
                'titulo'          => $p['titulo'],
                'total_puntos'    => $total,
                'puntos_obtenidos'=> $total > 0 ? round(($puntaje / 100) * $total, 2) : 0,
            ];
        }

        $this->render('actividad/resultado', [
            'titulo'      => 'Resultado',
            'menuActivo'  => 'actividades',
            'actividad'   => $actividad,
            'calificacion'=> $calificacion,
            'aprobado'    => $aprobado,
            'detalle'     => $detalle,
            'encodedId'   => $encodedId
        ]);
    }

    private function sanitizarHTML($html)
    {
        $permitidas = '<p><br><b><strong><i><em><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><code><pre><blockquote><table><thead><tbody><tr><th><td><hr><img>';
        $html = strip_tags($html, $permitidas);
        $html = preg_replace('/javascript:/i', '', $html);
        $html = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $html);
        return $html;
    }
}