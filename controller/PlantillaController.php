<?php
require_once __DIR__ . '/../core/Auth.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PlantillaController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }

    /**
     * Vista de seleccion de plantillas
     */
    public function index()
    {
        $this->render('plantilla/index', [
            'titulo'     => 'Plantillas de Trabajo',
            'menuActivo' => 'plantillas'
        ]);
    }

    public function descargar($tipo)
    {
        $plantillas = [
            'ers'           => 'ers',
            'stakeholders'  => 'stakeholders',
            'backlog'       => 'backlog',
            'riesgos'       => 'riesgos',
        ];

        if (!isset($plantillas[$tipo])) {
            $_SESSION['error'] = 'Plantilla no encontrada';
            $this->redirect('plantilla');
        }

        $archivo = $plantillas[$tipo];
        $html = $this->renderToString('plantilla/' . $archivo, [
            'nombreEstudiante' => \Auth::nombre(),
            'fecha'            => date('d/m/Y')
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('chroot', __DIR__ . '/..');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        if (ob_get_level()) ob_end_clean();

        $nombres = [
            'ers'           => 'ERS_Simplificada',
            'stakeholders'  => 'Matriz_Stakeholders',
            'backlog'       => 'Tabla_Backlog',
            'riesgos'       => 'Matriz_Riesgos',
        ];

        $filename = $nombres[$tipo] . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }

    private function renderToString($view, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../view/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("Vista no encontrada: $view");
        }
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }
}
