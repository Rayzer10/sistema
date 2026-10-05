<?php
require_once __DIR__ . '/../core/Auth.php';

class SeccionController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ADMIN);
    }

    public function index()
    {
        $seccionModel = $this->model('Seccion');
        $secciones = $seccionModel->listarCompleto();

        $this->render('seccion/index', [
            'titulo'     => 'Secciones',
            'menuActivo' => 'seccion',
            'secciones'  => $secciones
        ]);
    }

    public function form($encodedId = null)
    {
        $seccionModel = $this->model('Seccion');
        $periodoModel = $this->model('Periodo');
        $materiaModel = $this->model('Materia');
        $seccion = null;

        if ($encodedId) {
            $id = $this->decodeId($encodedId);
            $seccion = $seccionModel->findCompleto($id);
            if (!$seccion) {
                $_SESSION['error'] = 'Sección no encontrada';
                $this->redirect('seccion');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // El periodo se asigna automaticamente al crear.
            // Al editar se mantiene el periodo que ya tiene la seccion.
            if ($seccion) {
                $idPeriodo = (int)$seccion['id_periodo'];
            } else {
                $periodoVigente = $periodoModel->periodoActivo();
                if (!$periodoVigente) {
                    $_SESSION['error'] = 'No hay un período académico activo. Crea uno antes de registrar secciones.';
                    $this->redirect('seccion/form');
                }
                $idPeriodo = (int)$periodoVigente['id'];
            }

            $data = [
                'nombre'     => trim($_POST['nombre'] ?? ''),
                'id_periodo' => $idPeriodo,
            ];

            $idsMaterias = $_POST['materias'] ?? [];
            if (!is_array($idsMaterias)) $idsMaterias = [];
            $idsMaterias = array_values(array_map('intval', array_filter($idsMaterias, 'is_numeric')));
            $idsMaterias = array_unique($idsMaterias);

            $docentesPorMateria = $_POST['docentes_materia'] ?? [];
            if (!is_array($docentesPorMateria)) $docentesPorMateria = [];

            $errores = [];

            if (empty($data['nombre'])) {
                $errores[] = 'El nombre es obligatorio';
            } elseif (strlen($data['nombre']) > 80) {
                $errores[] = 'El nombre no puede exceder 80 caracteres';
            }

            // Cada materia seleccionada debe tener un docente que la imparta
            $asignaciones = [];
            foreach ($idsMaterias as $idMat) {
                $idDoc = (int)($docentesPorMateria[$idMat] ?? 0);
                if ($idDoc <= 0) {
                    $errores[] = 'Cada materia seleccionada debe tener un docente';
                    break;
                }
                $materiasDocente = $seccionModel->listarMateriasDelDocente($idDoc);
                $idsPermitidos = array_map(fn($m) => (int)$m['id'], $materiasDocente);
                if (!in_array($idMat, $idsPermitidos, true)) {
                    $errores[] = 'La materia seleccionada no la imparte el docente elegido';
                    break;
                }
                $asignaciones[] = ['id_materia' => $idMat, 'id_docente' => $idDoc];
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('seccion/form' . ($encodedId ? '/' . $encodedId : ''));
            }

            if ($seccion) {
                $seccionModel->update($seccion['id'], $data);
                $seccionModel->asignarMaterias($seccion['id'], $asignaciones);
                $_SESSION['success'] = 'Sección actualizada exitosamente';
            } else {
                $nuevaId = $seccionModel->create($data);
                $seccionModel->asignarMaterias($nuevaId, $asignaciones);
                $_SESSION['success'] = 'Sección creada exitosamente';
            }

            $this->redirect('seccion');
        }

        $periodoVigente = $periodoModel->periodoActivo();

        $materiasSeccion = [];
        if ($seccion) {
            $materiasSeccion = $seccionModel->materiasDeSeccion($seccion['id']);
        }

        // Mapa materia_id => [ids de docentes que imparten esa materia],
        // para cargar los selects de docente por materia en el frontend
        $materiasDocentes = [];
        foreach ($seccionModel->listarDocentes() as $d) {
            foreach ($seccionModel->listarMateriasDelDocente((int)$d['id']) as $m) {
                $materiasDocentes[(int)$m['id']][] = (int)$d['id'];
            }
        }

        $this->render('seccion/form', [
            'titulo'          => ($seccion ? 'Editar' : 'Nueva') . ' Sección',
            'menuActivo'      => 'seccion',
            'seccion'         => $seccion,
            'materias'        => $materiaModel->listarMaterias(),
            'docentes'        => $seccionModel->listarDocentes(),
            'materiasSeccion' => $materiasSeccion,
            'materiasDocentes'=> $materiasDocentes,
            'periodoVigente'  => $periodoVigente,
            'encodedId'       => $encodedId
        ]);
    }

    public function eliminar($encodedId)
    {
        $seccionModel = $this->model('Seccion');
        $id = $this->decodeId($encodedId);

        $seccionModel->delete($id);
        $_SESSION['success'] = 'Sección eliminada exitosamente';
        $this->redirect('seccion');
    }

    /**
     * Pantalla para asignar / quitar estudiantes a una seccion
     */
    public function asignar($encodedId)
    {
        $seccionModel = $this->model('Seccion');
        $id = $this->decodeId($encodedId);

        $seccion = $seccionModel->findCompleto($id);
        if (!$seccion) {
            $_SESSION['error'] = 'Sección no encontrada';
            $this->redirect('seccion');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $estudiantes = $_POST['estudiantes'] ?? [];
            if (!is_array($estudiantes)) $estudiantes = [];
            $estudiantes = array_map('intval', $estudiantes);

            $ok = $seccionModel->asignarEstudiantes($id, $estudiantes);
            $_SESSION['success'] = "Se asignaron {$ok} estudiante(s) a la sección";
            $this->redirect('seccion/asignar/' . $encodedId);
        }

        $asignados = $seccionModel->estudiantesAsignados($id);
        $disponibles = $seccionModel->listarEstudiantesDisponibles($id);

        $this->render('seccion/asignar', [
            'titulo'      => 'Asignar Estudiantes',
            'menuActivo'  => 'seccion',
            'seccion'     => $seccion,
            'asignados'   => $asignados,
            'disponibles' => $disponibles,
            'encodedId'   => $encodedId
        ]);
    }

    public function quitarEstudiante($encodedId, $encodedIdEst)
    {
        $seccionModel = $this->model('Seccion');
        $idSeccion = $this->decodeId($encodedId);
        $idEstudiante = $this->decodeId($encodedIdEst);

        $seccionModel->quitarEstudiante($idSeccion, $idEstudiante);
        $_SESSION['success'] = 'Estudiante removido de la sección';
        $this->redirect('seccion/asignar/' . $encodedId);
    }
}
