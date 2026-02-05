<?php
class AlumnosController extends Controller
{

    public function __construct()
    {
        // Verificar Sesión Base
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth');
        }

        // Fallback por si no se cargó el rol_id en sesión
        if (!isset($_SESSION['usuario_rol_id'])) {
            $userModel = $this->model('Usuario');
            $u = $userModel->obtenerPorId($_SESSION['usuario_id']);
            $_SESSION['usuario_rol_id'] = $u['rol_id'] ?? 5; // Socio por defecto
        }

        // Verificar Rol Admin/Profe
        if ($_SESSION['usuario_rol_id'] > 4) {
            $this->redirect('dashboard');
        }
    }

    public function index()
    {
        $alumnosModel = $this->model('AlumnosModel');

        // Filtros
        $busqueda = $_GET['q'] ?? '';
        // show_all=1 => NO solo activos (soloActivos = false). Default: soloActivos = true.
        $showAll = isset($_GET['show_all']) && $_GET['show_all'] == '1';
        $soloActivos = !$showAll;

        // Ordenamiento
        $sort = $_GET['sort'] ?? 'apellido';
        $dir = $_GET['dir'] ?? 'ASC';

        // Paginación
        $pagina = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 20;
        $offset = ($pagina - 1) * $limit;

        $resultado = $alumnosModel->listar($busqueda, $offset, $limit, $soloActivos, $sort, $dir);

        $datos = [
            'alumnos' => $resultado['data'],
            'total' => $resultado['total'],
            'paginas' => ceil($resultado['total'] / $limit),
            'pagina_actual' => $pagina,
            'busqueda' => $busqueda,
            'filters' => [
                'show_all' => $showAll,
                'sort' => $sort,
                'dir' => $dir
            ]
        ];

        $this->view('alumnos/index', $datos);
    }

    public function crear()
    {
        $this->view('alumnos/crear');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alumnosModel = $this->model('AlumnosModel');

            $dni = $_POST['dni'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $telefono = $_POST['telefono'] ?? '';

            $errores = [];

            if (empty($dni) || empty($nombre) || empty($apellido)) {
                $errores[] = "DNI, Nombre y Apellido son obligatorios.";
            }

            if ($alumnosModel->verificarDni($dni)) {
                $errores[] = "El DNI ya está registrado.";
            }

            if (!empty($errores)) {
                $this->view('alumnos/crear', ['errores' => $errores, 'old' => $_POST]);
                return;
            }

            $datos = [
                'dni' => $dni,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'telefono' => $telefono
            ];

            if ($alumnosModel->agregar($datos)) {
                $this->redirect('alumnos?status=created');
            } else {
                $this->view('alumnos/crear', ['errores' => ['Error al guardar en base de datos.']]);
            }
        }
    }
}
?>