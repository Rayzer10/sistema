<?php
require_once __DIR__ . '/../core/Auth.php';

class NivelController extends Controller
{
    public function __construct()
    {
        $this->requireRole(Auth::ROL_ADMIN);
    }

    public function index()
    {
        $nivelModel = $this->model('Nivel');
        $niveles = $nivelModel->conUnidadesPublicadas();

        $this->render('nivel/index', [
            'titulo'     => 'Niveles de Contenido',
            'menuActivo' => 'nivel',
            'niveles'    => $niveles
        ]);
    }
}
