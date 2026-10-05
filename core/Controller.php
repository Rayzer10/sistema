<?php
require_once __DIR__ . '/Auth.php';

class Controller
{
    protected function model($model)
    {
        $modelFile = __DIR__ . '/../model/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        return null;
    }

    protected function view($view, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../view/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Vista no encontrada: " . $view);
        }
    }

    /**
     * Renderiza una vista dentro del layout principal (sidebar + navbar)
     */
    protected function render($view, $data = [])
    {
        ob_start();
        extract($data);
        $viewFile = __DIR__ . '/../view/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("Vista no encontrada: " . $view);
        }
        $contenido = ob_get_clean();

        $layoutData = array_merge($data, ['contenido' => $contenido]);
        extract($layoutData);
        require __DIR__ . '/../view/layout/main.php';
    }

    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    /**
     * Codifica un ID en base64 (para URLs)
     */
    protected function encodeId($id)
    {
        return rtrim(strtr(base64_encode($id), '+/', '-_'), '=');
    }

    /**
     * Decodifica un ID desde base64
     */
    protected function decodeId($encoded)
    {
        return base64_decode(strtr($encoded, '-_', '+/'));
    }

    /**
     * Verifica que el usuario este autenticado.
     * Si no, redirige al login.
     */
    protected function requireAuth()
    {
        if (!Auth::check()) {
            $this->redirect('');
        }
    }

    /**
     * Verifica que el usuario tenga uno de los roles permitidos.
     * Si no, redirige al home con mensaje.
     *
     * @param int|array $roles  Rol o lista de roles permitidos
     */
    protected function requireRole($roles)
    {
        $this->requireAuth();

        if (!Auth::hasRole($roles)) {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            $this->redirect('home');
        }
    }
}
