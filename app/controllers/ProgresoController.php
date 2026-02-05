<?php
class ProgresoController extends Controller
{

    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth');
        }
    }

    public function index()
    {
        $model = $this->model('ProgresoModel');
        $ejercicios = $model->obtenerEjerciciosDisponibles($_SESSION['usuario_id']);

        $this->view('progreso.index', ['ejercicios' => $ejercicios]);
    }

    public function data()
    {
        // API endpoint para Chart.js
        $ejercicioId = $_GET['id'] ?? 0;
        if (!$ejercicioId) {
            echo json_encode([]);
            exit;
        }

        $model = $this->model('ProgresoModel');
        $historial = $model->obtenerHistorialEjercicio($_SESSION['usuario_id'], $ejercicioId);

        header('Content-Type: application/json');
        echo json_encode($historial);
        exit;
    }
}
?>