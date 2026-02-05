<?php
class FinanzasController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth');
        }
        // Rol Check: 1 or 2 (Admin/Profe) only. Ideally only Admin (1) for Finance.
        if ($_SESSION['usuario_rol_id'] > 2) {
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }
    }

    public function index()
    {
        $model = $this->model('FinanzasModel');

        $datos = [
            'ingresos_hoy' => $model->obtenerIngresosHoy(),
            'ingresos_mes' => $model->obtenerIngresosMes(),
            'efectivo_caja' => $model->obtenerEfectivoCaja(),
            'ultimos_movimientos' => $model->obtenerUltimosMovimientos(10),
            'metodos_pago_chart' => $model->obtenerDistribucionMetodos()
        ];

        $this->view('finanzas/index', $datos);
    }

    // Method to manually register a payment
    public function registrarPago()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        $model = $this->model('FinanzasModel');
        $alumno_id = $_POST['alumno_id'];
        $monto = $_POST['monto'];
        $metodo = $_POST['metodo_pago']; // 'efectivo', 'transferencia', 'otro'
        $concepto = $_POST['concepto'];
        $observaciones = $_POST['observaciones'] ?? '';

        $res = $model->registrarPago($alumno_id, $monto, $concepto, $metodo, $observaciones);

        if ($res) {
            // If payment is for a monthly fee, we should update the student's expiration date too.
            // This logic might be in AlumnosModel but acts here as a transaction.
            $alumnosModel = $this->model('AlumnosModel');
            $alumnosModel->renovarCuota($alumno_id);

            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false]);
        }
    }
}
