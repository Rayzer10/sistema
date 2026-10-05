<?php
require_once __DIR__ . '/../core/Auth.php';

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ADMIN);
    }

    public function index()
    {
        $usuarioModel = $this->model('Usuario');
        $usuarios = $usuarioModel->all();
        $this->render('usuario/index', [
            'titulo' => 'Listado de Usuarios',
            'menuActivo' => 'usuario',
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Maneja tanto crear como editar según si recibe ID o no
     * URL: /usuario/form o /usuario/form/{id_codificado}
     */
    public function form($encodedId = null)
    {
        $usuarioModel = $this->model('Usuario');
        $materiaModel = $this->model('Materia');
        $dmModel = $this->model('DocenteMateria');
        $usuario = null;

        if ($encodedId) {
            $id = $this->decodeId($encodedId);
            $usuario = $usuarioModel->find($id);
            if (!$usuario) {
                $_SESSION['error'] = 'Usuario no encontrado';
                $this->redirect('usuario');
            }
        }

        $materiasAsignadas = [];
        if ($usuario && (int)$usuario['rol'] === Auth::ROL_DOCENTE) {
            $rows = $dmModel->materiasDeDocente($usuario['id']);
            $materiasAsignadas = array_map(fn($m) => (int)$m['id'], $rows);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'   => trim($_POST['nombre'] ?? ''),
                'correo'   => trim($_POST['correo'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'rol'      => (int)($_POST['rol'] ?? Auth::ROL_ESTUDIANTE),
                'estado'   => (int)($_POST['estado'] ?? 1),
            ];

            $errores = [];

            if (empty($data['nombre'])) {
                $errores[] = 'El nombre es obligatorio';
            } elseif (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ ]+$/', $data['nombre'])) {
                $errores[] = 'El nombre solo puede contener letras y espacios';
            }

            if (empty($data['correo'])) {
                $errores[] = 'El correo es obligatorio';
            } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo no tiene un formato válido';
            }

            if (!empty($data['telefono']) && !preg_match('/^[0-9]+$/', $data['telefono'])) {
                $errores[] = 'El teléfono solo puede contener números';
            }

            if (!in_array($data['rol'], [Auth::ROL_ADMIN, Auth::ROL_DOCENTE, Auth::ROL_ESTUDIANTE], true)) {
                $errores[] = 'Rol inválido';
            }

            $clave = $_POST['clave'] ?? '';
            $nuevoHash = null;
            if ($usuario) {
                if (!empty($clave)) {
                    if (strlen($clave) < 8 || !preg_match('/^[A-Za-z0-9]+$/', $clave)) {
                        $errores[] = 'La contraseña debe tener mínimo 8 caracteres alfanuméricos';
                    } else {
                        $nuevoHash = password_hash($clave, PASSWORD_DEFAULT);
                    }
                }
            } else {
                if (empty($clave)) {
                    $errores[] = 'La contraseña es obligatoria';
                } elseif (strlen($clave) < 8 || !preg_match('/^[A-Za-z0-9]+$/', $clave)) {
                    $errores[] = 'La contraseña debe tener mínimo 8 caracteres alfanuméricos';
                } else {
                    $nuevoHash = password_hash($clave, PASSWORD_DEFAULT);
                }
            }

            $excluirId = $usuario ? $usuario['id'] : null;
            if ($usuarioModel->existeCorreo($data['correo'], $excluirId)) {
                $errores[] = 'Ya existe un usuario con este correo electrónico';
            }

            if (!empty($errores)) {
                $_SESSION['error'] = implode('<br>', $errores);
                $this->redirect('usuario/form' . ($encodedId ? '/' . $encodedId : ''));
            }

            if ($usuario) {
                $usuarioModel->update($usuario['id'], $data);
                if ($nuevoHash) {
                    $usuarioModel->actualizarClave($usuario['id'], $clave);
                }
                $idUsuario = $usuario['id'];
            } else {
                $data['clave'] = $nuevoHash;
                $idUsuario = $usuarioModel->create($data);
            }

            if ($data['rol'] === Auth::ROL_DOCENTE) {
                $idsMaterias = $_POST['materias'] ?? [];
                if (!is_array($idsMaterias)) $idsMaterias = [];
                $dmModel->asignar($idUsuario, array_map('intval', $idsMaterias));
            }

            $_SESSION['success'] = 'Usuario guardado exitosamente';
            $this->redirect('usuario');
        }

        $this->render('usuario/form', [
            'titulo'     => ($usuario ? 'Editar' : 'Agregar') . ' Usuario',
            'menuActivo' => 'usuario',
            'usuario'    => $usuario,
            'materias'   => $materiaModel->listarMaterias(),
            'materiasAsignadas' => $materiasAsignadas,
            'encodedId'  => $encodedId
        ]);
    }

    public function eliminar($encodedId)
    {
        $usuarioModel = $this->model('Usuario');
        $id = $this->decodeId($encodedId);

        if ((int)$id === (int)Auth::id()) {
            $_SESSION['error'] = 'No puedes eliminar tu propio usuario';
            $this->redirect('usuario');
        }

        $usuarioModel->delete($id);
        $_SESSION['success'] = 'Usuario eliminado exitosamente';
        $this->redirect('usuario');
    }

    public function perfil()
    {
        $this->requireAuth();

        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->find(Auth::id());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'   => trim($_POST['nombre'] ?? ''),
                'correo'   => trim($_POST['correo'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
            ];

            if (empty($data['nombre'])) {
                $_SESSION['error'] = 'El nombre es obligatorio';
                $this->redirect('usuario/perfil');
            } elseif (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ ]+$/', $data['nombre'])) {
                $_SESSION['error'] = 'El nombre solo puede contener letras y espacios';
                $this->redirect('usuario/perfil');
            }

            if (empty($data['correo'])) {
                $_SESSION['error'] = 'El correo es obligatorio';
                $this->redirect('usuario/perfil');
            } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'El correo no tiene un formato válido';
                $this->redirect('usuario/perfil');
            }

            if (!empty($data['telefono']) && !preg_match('/^[0-9]+$/', $data['telefono'])) {
                $_SESSION['error'] = 'El teléfono solo puede contener números';
                $this->redirect('usuario/perfil');
            }

            if ($usuarioModel->existeCorreo($data['correo'], $usuario['id'])) {
                $_SESSION['error'] = 'Ya existe otro usuario con este correo electrónico';
                $this->redirect('usuario/perfil');
            }

            $clave = $_POST['clave'] ?? '';
            if (!empty($clave)) {
                if (strlen($clave) < 8 || !preg_match('/^[A-Za-z0-9]+$/', $clave)) {
                    $_SESSION['error'] = 'La contraseña debe tener mínimo 8 caracteres alfanuméricos';
                    $this->redirect('usuario/perfil');
                }
                $usuarioModel->actualizarClave($usuario['id'], $clave);
            }

            $usuarioModel->update($usuario['id'], $data);

            $usuarioActualizado = $usuarioModel->find($usuario['id']);
            $_SESSION['usuario'] = $usuarioActualizado;

            $_SESSION['success'] = 'Perfil actualizado exitosamente';
            $this->redirect('usuario/perfil');
        }

        $this->render('usuario/form', [
            'titulo'  => 'Mi Perfil',
            'menuActivo' => 'perfil',
            'usuario' => $usuario,
            'esPerfil' => true,
            'encodedId' => $this->encodeId($usuario['id'])
        ]);
    }
}
