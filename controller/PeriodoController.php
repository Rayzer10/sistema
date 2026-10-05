<?php
require_once __DIR__ . '/../core/Auth.php';

class PeriodoController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ADMIN);
    }

    public function index()
    {
        $periodoModel = $this->model('Periodo');
        $periodos = $periodoModel->all();

        foreach ($periodos as &$p) {
            $p['total_secciones'] = $periodoModel->conSecciones($p['id']);
        }

        $this->render('periodo/index', [
            'titulo'     => 'Períodos Académicos',
            'menuActivo' => 'periodo',
            'periodos'   => $periodos
        ]);
    }

    public function form($encodedId = null)
    {
        $periodoModel = $this->model('Periodo');
        $periodo = null;

        if ($encodedId) {
            $id = $this->decodeId($encodedId);
            $periodo = $periodoModel->find($id);
            if (!$periodo) {
                $_SESSION['error'] = 'Período no encontrado';
                $this->redirect('periodo');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'       => trim($_POST['nombre'] ?? ''),
                'fecha_inicio' => trim($_POST['fecha_inicio'] ?? ''),
                'fecha_fin'    => trim($_POST['fecha_fin'] ?? ''),
                'estado'       => (int)($_POST['estado'] ?? 1),
            ];

            $errores = [];

            if (empty($data['nombre'])) {
                $errores[] = 'El nombre es obligatorio';
            } elseif (strlen($data['nombre']) > 80) {
                $errores[] = 'El nombre no puede exceder 80 caracteres';
            }

            if (empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
                $errores[] = 'Las fechas de inicio y fin son obligatorias';
            } else {
                $d1 = DateTime::createFromFormat('Y-m-d', $data['fecha_inicio']);
                $d2 = DateTime::createFromFormat('Y-m-d', $data['fecha_fin']);
                if (!$d1 || $d1->format('Y-m-d') !== $data['fecha_inicio']) {
                    $errores[] = 'La fecha de inicio no es válida';
                }
                if (!$d2 || $d2->format('Y-m-d') !== $data['fecha_fin']) {
                    $errores[] = 'La fecha de fin no es válida';
                }
                if (empty($errores) && $d1 && $d2 && $d1 >= $d2) {
                    $errores[] = 'La fecha de fin debe ser posterior a la fecha de inicio';
                }
            }

            if (!in_array($data['estado'], [1, 2], true)) {
                $errores[] = 'Estado inválido';
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('periodo/form' . ($encodedId ? '/' . $encodedId : ''));
            }

            if ($periodo) {
                $periodoModel->update($periodo['id'], $data);
                $_SESSION['success'] = 'Período actualizado exitosamente';
            } else {
                $periodoModel->create($data);
                $_SESSION['success'] = 'Período creado exitosamente';
            }

            $this->redirect('periodo');
        }

        $this->render('periodo/form', [
            'titulo'     => ($periodo ? 'Editar' : 'Nuevo') . ' Período',
            'menuActivo' => 'periodo',
            'periodo'    => $periodo,
            'encodedId'  => $encodedId
        ]);
    }

    public function eliminar($encodedId)
    {
        $periodoModel = $this->model('Periodo');
        $id = $this->decodeId($encodedId);

        if ($periodoModel->conSecciones($id) > 0) {
            $_SESSION['error'] = 'No se puede eliminar: el período tiene secciones asociadas';
            $this->redirect('periodo');
        }

        $periodoModel->delete($id);
        $_SESSION['success'] = 'Período eliminado exitosamente';
        $this->redirect('periodo');
    }
}
