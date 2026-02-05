<?php
class DashboardController extends Controller
{

    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth');
        }
    }

    public function index()
    {
        $dashModel = $this->model('DashboardModel');
        $usuarioId = $_SESSION['usuario_id'];

        // Asumiendo que guardaste el rol en sesión al loguear. 
        // Si no está, lo buscamos (debería estar en AuthController)
        // Por seguridad, consultamos el rol rápido o confiamos en session si ya validamos
        // Vamos a asumir que SI existe $_SESSION['usuario_rol'] o lo simulamos si es < 3 es admin/profe

        // Simulación temporal si no existe la variable: Buscar rol en BD
        if (!isset($_SESSION['usuario_rol_id'])) {
            // Fallback rápido (idealmente esto va en Auth)
            $userModel = $this->model('Usuario');
            $u = $userModel->obtenerPorId($usuarioId);
            $_SESSION['usuario_rol_id'] = $u['rol_id'];
        }

        // Registrar Uso de App (Log)
        $dashModel->registrarUsoApp($usuarioId);

        // Obtener Anuncios Activos
        $anunciosModel = $this->model('AnunciosModel');
        $anuncios = $anunciosModel->obtenerActivos();

        $rolId = $_SESSION['usuario_rol_id'];

        if ($rolId <= 4) {
            // ADMIN / PROFE 
            $datos = [
                'usuario' => $_SESSION['usuario_nombre'],
                'anuncios' => $anuncios,
                'activos' => $dashModel->contarSociosActivos(),
                'nuevos' => $dashModel->contarNuevosMes(),
                'hoy' => $dashModel->contarAsistenciasHoy(),
                'en_gym' => $dashModel->contarEnGymAhora(),
                'ultimo_acceso' => $dashModel->obtenerUltimoAcceso(),
                'flujo' => $dashModel->obtenerFlujoAsistencias(),
                'vencimientos' => $dashModel->obtenerVencimientosProximos(),
                'membresias' => $dashModel->obtenerEstadoMembresias()
            ];
            $this->view('dashboard.admin', $datos);

        } else {
            // ALUMNO
            $datos = [
                'usuario' => $_SESSION['usuario_nombre'],
                'anuncios' => $anuncios,
                'plan' => $dashModel->obtenerPlanActual($usuarioId),
                'asistencias' => $dashModel->asistenciasMes($usuarioId)
            ];
            $this->view('dashboard.index', $datos);
        }
    }

    public function api_live_metrics()
    {
        // Validación básica de rol (puedes mejorar en middleware)
        if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] > 4) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        $dashModel = $this->model('DashboardModel');

        $response = [
            'hoy' => $dashModel->contarAsistenciasHoy(),
            'ultimo_acceso' => $dashModel->obtenerUltimoAcceso(),
            'activos' => $dashModel->contarSociosActivos(),
            'en_gym' => $dashModel->contarEnGymAhora()
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function api_app_usage()
    {
        if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] > 4) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        $dashModel = $this->model('DashboardModel');
        $fecha = $_GET['fecha'] ?? date('Y-m-d');

        $data = $dashModel->obtenerUsoAppPorFecha($fecha);

        header('Content-Type: application/json');
        echo json_encode([
            'count' => count($data),
            'list' => $data
        ]);
        exit;
    }
}
?>