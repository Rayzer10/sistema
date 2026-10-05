<?php
require_once __DIR__ . '/../core/Auth.php';

class DinamicaController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ESTUDIANTE);
    }

    /**
     * Lista de dinamicas disponibles (agrupadas por nivel).
     * Respeta el desbloqueo progresivo.
     */
    public function index()
    {
        $dinamicaModel = $this->model('Dinamica');
        $progresoModel = $this->model('ProgresoUnidad');

        $dinamicas = $dinamicaModel->listarParaEstudiante(Auth::id(), $progresoModel);

        // Agrupar por nivel
        $porNivel = [];
        foreach ($dinamicas as $d) {
            $porNivel[(int)$d['id_nivel']][] = $d;
        }

        $nivelModel = $this->model('Nivel');
        $niveles = $nivelModel->todos();

        $this->render('dinamica/index', [
            'titulo'     => 'Laboratorio de Dinámicas',
            'menuActivo' => 'dinamicas',
            'porNivel'   => $porNivel,
            'niveles'    => $niveles
        ]);
    }

    /**
     * Inicia o continua un intento, muestra la primera pregunta
     */
    public function jugar($encodedId)
    {
        $dinamicaModel = $this->model('Dinamica');
        $intentoModel = $this->model('IntentoDinamica');
        $progresoModel = $this->model('ProgresoUnidad');

        $id = $this->decodeId($encodedId);
        $dinamica = $dinamicaModel->conPreguntas($id);
        if (!$dinamica) {
            $_SESSION['error'] = 'Dinámica no encontrada';
            $this->redirect('dinamica');
        }

        if (!$progresoModel->nivelDesbloqueado(Auth::id(), $dinamica['id_nivel'])) {
            $_SESSION['error'] = 'Esta dinámica pertenece a un nivel bloqueado';
            $this->redirect('dinamica');
        }

        if (empty($dinamica['preguntas'])) {
            $_SESSION['error'] = 'Esta dinámica no tiene preguntas configuradas';
            $this->redirect('dinamica');
        }

        // Buscar intento en curso; si no, crear uno nuevo
        $intento = $intentoModel->intentoEnCurso(Auth::id(), $id);
        if (!$intento) {
            $idIntento = $intentoModel->iniciar(Auth::id(), $id, count($dinamica['preguntas']));
            $intento = ['id' => $idIntento, 'id_estudiante' => Auth::id(), 'id_dinamica' => $id];
        }

        $this->render('dinamica/jugar', [
            'titulo'     => $dinamica['titulo'],
            'menuActivo' => 'dinamicas',
            'dinamica'   => $dinamica,
            'intento'    => $intento,
            'encodedId'  => $encodedId
        ]);
    }

    /**
     * Recibe la respuesta a una pregunta, da feedback inmediato y avanza
     */
    public function responder($encodedId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dinamica');
        }

        $dinamicaModel = $this->model('Dinamica');
        $intentoModel = $this->model('IntentoDinamica');

        $idDinamica = $this->decodeId($encodedId);
        $dinamica = $dinamicaModel->conPreguntas($idDinamica);
        if (!$dinamica) {
            $_SESSION['error'] = 'Dinámica no encontrada';
            $this->redirect('dinamica');
        }

        $idIntento = (int)($_POST['id_intento'] ?? 0);
        $idPregunta = (int)($_POST['id_pregunta'] ?? 0);
        $idOpcion = (int)($_POST['id_opcion'] ?? 0);

        if ($idIntento <= 0 || $idPregunta <= 0 || $idOpcion <= 0) {
            $this->redirect('dinamica/jugar/' . $encodedId);
        }

        // Buscar la opcion y verificar que pertenece a la pregunta
        $opcion = null;
        foreach ($dinamica['preguntas'] as $p) {
            if ((int)$p['id'] === $idPregunta) {
                foreach ($p['opciones'] as $o) {
                    if ((int)$o['id'] === $idOpcion) {
                        $opcion = $o;
                        break 2;
                    }
                }
            }
        }
        if (!$opcion) {
            $this->redirect('dinamica/jugar/' . $encodedId);
        }

        $intentoModel->registrarRespuesta($idIntento, $idPregunta, $idOpcion, $opcion['es_correcta'] == 1);

        $this->render('dinamica/feedback', [
            'titulo'     => $dinamica['titulo'],
            'menuActivo' => 'dinamicas',
            'dinamica'   => $dinamica,
            'opcion'     => $opcion,
            'pregunta'   => $p ?? null,
            'intento'    => ['id' => $idIntento],
            'encodedId'  => $encodedId
        ]);
    }

    /**
     * Finaliza el intento y muestra resultados
     */
    public function finalizar($encodedId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dinamica');
        }

        $intentoModel = $this->model('IntentoDinamica');

        $idIntento = (int)($_POST['id_intento'] ?? 0);
        if ($idIntento <= 0) {
            $this->redirect('dinamica');
        }

        $resultado = $intentoModel->finalizar($idIntento);

        $this->redirect('dinamica/resultado/' . $this->encodeId($idIntento));
    }

    public function resultado($encodedIdIntento)
    {
        $intentoModel = $this->model('IntentoDinamica');
        $idIntento = $this->decodeId($encodedIdIntento);

        $intento = $intentoModel->findCompleto($idIntento);
        if (!$intento || (int)$intento['id_estudiante'] !== Auth::id()) {
            $_SESSION['error'] = 'Resultado no disponible';
            $this->redirect('dinamica');
        }

        $this->render('dinamica/resultado', [
            'titulo'     => 'Resultado',
            'menuActivo' => 'dinamicas',
            'intento'    => $intento,
            'encodedId'  => $encodedIdIntento
        ]);
    }
}
