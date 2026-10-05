<?php
class HomeController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Panel Principal',
            'menuActivo' => 'home'
        ];

        if (\Auth::isEstudiante()) {
            $seccionModel = $this->model('Seccion');
            $data['materiasEstudiante'] = $seccionModel->materiasDeEstudiante(\Auth::id());
        }

        $this->render('home/index', $data);
    }
}
