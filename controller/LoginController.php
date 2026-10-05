<?php
require_once __DIR__ . '/../core/Auth.php';

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $this->redirect('home');
        }
        $this->view('login/login');
    }

    public function autenticar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
        }

        $correo = trim($_POST['correo'] ?? '');
        $clave = $_POST['clave'] ?? '';

        if (empty($correo) || empty($clave)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            $_SESSION['old_correo'] = $correo;
            session_write_close();
            $this->redirect('');
        }

        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->autenticar($correo, $clave);

        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            unset($_SESSION['error']);
            session_write_close();
            $this->redirect('home');
        } else {
            $_SESSION['error'] = 'Correo o contraseña incorrectos';
            $_SESSION['old_correo'] = $correo;
            session_write_close();
            $this->redirect('');
        }
    }

    public function loginAjax()
    {
        ob_start();

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response = ['success' => false, 'message' => 'Método no permitido'];
            } else {
                $input = json_decode(file_get_contents('php://input'), true);
                $correo = trim($input['correo'] ?? '');
                $clave = $input['clave'] ?? '';

                if (empty($correo) || empty($clave)) {
                    $response = ['success' => false, 'message' => 'Todos los campos son obligatorios'];
                } else {
                    $usuarioModel = $this->model('Usuario');
                    $usuario = $usuarioModel->autenticar($correo, $clave);

                    if ($usuario) {
                        $_SESSION['usuario'] = $usuario;
                        $response = ['success' => true, 'redirect' => BASE_URL . 'home'];
                    } else {
                        $response = ['success' => false, 'message' => 'Correo o contraseña incorrectos'];
                    }
                }
            }
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Error de conexión con el servidor: ' . $e->getMessage()];
        } catch (Throwable $e) {
            $response = ['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()];
        }

        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('');
    }
}
