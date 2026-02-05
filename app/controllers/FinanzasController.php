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

        $fechaFiltro = $_GET['fecha'] ?? null;

        // If date filter is active, we might want to increase limit or remove it
        $limit = $fechaFiltro ? 500 : 50;

        $datos = [
            'ingresos_hoy' => $model->obtenerIngresosHoy(), // This is 'Total Generated', logic remains 'Today' usually, or 'Shift'? Let's keep Today for KPI
            'ingresos_mes' => $model->obtenerIngresosMes(),
            'efectivo_caja' => $model->obtenerEfectivoCaja(),
            'ingresos_efectivo_hoy' => $model->obtenerIngresosEfectivoHoy(), // Shift logic
            'retiros_hoy' => $model->obtenerRetirosHoy(), // Shift logic
            'transferencias_hoy' => $model->obtenerEstadisticasTransferencias(), // Shift logic
            'ultimos_movimientos' => $model->obtenerMovimientosUnificados($limit, $fechaFiltro),
            'metodos_pago_chart' => $model->obtenerDistribucionMetodos(),
            'ultimo_arqueo' => $model->obtenerUltimoArqueo(),
            'fecha_filtro' => $fechaFiltro
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
        $destino = $_POST['destino'] ?? null; // Fernando/Matias

        $res = $model->registrarPago($alumno_id, $monto, $concepto, $metodo, $observaciones, $destino);

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

    // API to search student by DNI
    public function buscarAlumno()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        $dni = $_POST['dni'] ?? '';
        if (empty($dni)) {
            echo json_encode(['ok' => false, 'error' => 'DNI vacío']);
            return;
        }

        $model = $this->model('AlumnosModel');
        $alumno = $model->buscarPorDni($dni);

        if ($alumno) {
            // Check formatted expiration
            $alumno['vence_fmt'] = date('d/m/Y', strtotime($alumno['vence']));
            $alumno['estado'] = (strtotime($alumno['vence']) >= strtotime(date('Y-m-d'))) ? 'AL DÍA' : 'VENCIDO';
            echo json_encode(['ok' => true, 'alumno' => $alumno]);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Alumno no encontrado']);
        }
    }

    // Registrar Retiro de Caja
    public function registrarRetiro()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        $monto = $_POST['monto'];
        $concepto = $_POST['concepto'];
        $usuario_id = $_SESSION['usuario_id'];

        if (empty($monto) || $monto <= 0 || empty($concepto)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
            return;
        }

        $model = $this->model('FinanzasModel');
        $res = $model->registrarRetiro($usuario_id, $monto, $concepto);

        if ($res === true) {
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false, 'error' => $res]);
        }
    }

    public function registrarArqueo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        $usuario_id = $_SESSION['usuario_id'];
        $datos = [
            'usuario_id' => $usuario_id,
            'ingresos_sistema' => $_POST['ingresos_sistema'],
            'retiros_sistema' => $_POST['retiros_sistema'],
            'saldo_sistema' => $_POST['saldo_sistema'], // Expected
            'efectivo_real' => $_POST['efectivo_real'], // Counted
            'diferencia' => $_POST['diferencia'],
            'observaciones' => $_POST['observaciones'] ?? ''
        ];

        $model = $this->model('FinanzasModel');
        $res = $model->registrarArqueo($datos);

        if ($res === true) {
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false, 'error' => $res]);
        }
    }

    public function eliminarArqueo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['ok' => false, 'error' => 'ID inválido']);
            return;
        }

        $model = $this->model('FinanzasModel');
        if ($model->eliminarArqueo($id)) {
            echo json_encode(['ok' => true]);
        } else {
            echo json_encode(['ok' => false, 'error' => 'Error al eliminar']);
        }
    }
}
