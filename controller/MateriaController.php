<?php
require_once __DIR__ . '/../core/Auth.php';

class MateriaController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ADMIN);
    }

    public function index()
    {
        $materiaModel = $this->model('Materia');
        $materias = $materiaModel->conConteo();

        $this->render('materia/index', [
            'titulo'     => 'Materias',
            'menuActivo' => 'materia',
            'materias'   => $materias
        ]);
    }

    public function form($encodedId = null)
    {
        $materiaModel = $this->model('Materia');
        $materia = null;

        if ($encodedId) {
            $id = $this->decodeId($encodedId);
            $materia = $materiaModel->find($id);
            if (!$materia) {
                $_SESSION['error'] = 'Materia no encontrada';
                $this->redirect('materia');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');

            $errores = [];
            if (empty($nombre)) {
                $errores[] = 'El nombre es obligatorio';
            } elseif (strlen($nombre) > 120) {
                $errores[] = 'El nombre no puede exceder 120 caracteres';
            } elseif (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s\-\.\(\)]+$/', $nombre)) {
                $errores[] = 'El nombre contiene caracteres no permitidos';
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('materia/form' . ($encodedId ? '/' . $encodedId : ''));
            }

            if ($materia) {
                $materiaModel->update($materia['id'], ['nombre' => $nombre]);
                $_SESSION['success'] = 'Materia actualizada exitosamente';
            } else {
                $materiaModel->create(['nombre' => $nombre]);
                $_SESSION['success'] = 'Materia creada exitosamente';
            }

            $this->redirect('materia');
        }

        $this->render('materia/form', [
            'titulo'     => ($materia ? 'Editar' : 'Nueva') . ' Materia',
            'menuActivo' => 'materia',
            'materia'    => $materia,
            'encodedId'  => $encodedId
        ]);
    }

    public function eliminar($encodedId)
    {
        $materiaModel = $this->model('Materia');
        $id = $this->decodeId($encodedId);

        $db = \Database::getInstance();
        $sql = "SELECT COUNT(*) AS total FROM seccion_materia WHERE id_materia = ?";
        $row = $db->fetch($sql, [$id]);

        if ((int)$row['total'] > 0) {
            $_SESSION['error'] = 'No se puede eliminar: la materia está asignada a secciones';
            $this->redirect('materia');
        }

        $materiaModel->delete($id);
        $_SESSION['success'] = 'Materia eliminada exitosamente';
        $this->redirect('materia');
    }
}
