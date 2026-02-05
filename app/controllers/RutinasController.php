<?php
class RutinasController extends Controller
{

    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth');
        }

        // Fallback por si no se cargó el rol_id en sesión
        if (!isset($_SESSION['usuario_rol_id'])) {
            $userModel = $this->model('Usuario');
            $u = $userModel->obtenerPorId($_SESSION['usuario_id']);
            $_SESSION['usuario_rol_id'] = $u['rol_id'] ?? 5; // Socio por defecto
        }
    }

    public function index()
    {
        $model = $this->model('RutinasModel');
        $usuarioId = $_SESSION['usuario_id'];

        $semana = $_GET['semana'] ?? null;
        $anio = $_GET['anio'] ?? null;

        $rutina = $model->obtenerRutinaSemanal($usuarioId, $semana, $anio);

        $this->view('rutinas.index', ['rutina' => $rutina]);
    }

    public function dia($dia)
    {
        if (empty($dia))
            $this->redirect('rutinas');

        $model = $this->model('RutinasModel');
        $usuarioId = $_SESSION['usuario_id'];

        // Leer parámetros de tiempo opcionales para navegación histórica
        $semana = $_GET['semana'] ?? null;
        $anio = $_GET['anio'] ?? null;

        $rutinaHead = $model->obtenerRutinaSemanal($usuarioId, $semana, $anio);
        if (!$rutinaHead)
            $this->redirect('rutinas');

        $ejercicios = $model->obtenerEjerciciosPorDia(
            $usuarioId,
            $rutinaHead['semana'],
            $rutinaHead['anio'],
            $dia
        );

        // ENRIQUECER CON SERIES DETALLADAS
        foreach ($ejercicios as &$ej) {
            $ej['detalle_series'] = $model->obtenerSeries($ej['rutina_id']);
        }

        $this->view('rutinas.dia', [
            'dia' => $dia,
            'semana' => $rutinaHead['semana'],
            'anio' => $rutinaHead['anio'],
            'rutinaFull' => $rutinaHead, // Pasar estructura completa para navegación
            'ejercicios' => $ejercicios
        ]);
    }

    // --- ENDPOINTS PARA EDICIÓN ---
    public function update_series()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;
            $reps = $data['reps'] ?? 0;
            $peso = $data['peso'] ?? 0;

            if ($id) {
                $model = $this->model('RutinasModel');
                $res = $model->actualizarSerie($id, $reps, $peso);
                echo json_encode(['ok' => $res]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }

    public function add_serie()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $rutina_id = $data['rutina_id'] ?? 0;
            $reps = $data['reps'] ?? 0;
            $peso = $data['peso'] ?? 0;

            if ($rutina_id) {
                $model = $this->model('RutinasModel');
                $newId = $model->insertarSerie($rutina_id, $reps, $peso);
                echo json_encode(['ok' => !!$newId, 'id' => $newId]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }

    public function delete_serie()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id) {
                $model = $this->model('RutinasModel');
                $res = $model->eliminarSerie($id);
                echo json_encode(['ok' => $res]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }

    public function eliminar_ejercicio()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id) {
                $model = $this->model('RutinasModel');
                $res = $model->eliminarEjercicio($id);
                echo json_encode(['ok' => $res]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }

    public function toggle()
    {
        // AJAX endpoint
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id) {
                $model = $this->model('RutinasModel');
                $estado = $model->marcarEjercicioCompletado($id);
                echo json_encode(['ok' => true, 'completado' => $estado]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }

    // --- MÉTODOS PARA ADMIN (VER USUARIO) ---
    public function ver_cliente($id)
    {
        if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] > 4) {
            $this->redirect('dashboard');
        }

        $model = $this->model('RutinasModel');

        $semana = $_GET['semana'] ?? null;
        $anio = $_GET['anio'] ?? null;

        $rutina = $model->obtenerRutinaSemanal($id, $semana, $anio);

        $this->view('rutinas.index', ['rutina' => $rutina, 'admin_mode_user_id' => $id]);
    }

    public function dia_cliente($userId, $dia)
    {
        if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] > 4) {
            $this->redirect('dashboard');
        }

        $model = $this->model('RutinasModel');

        $semana = $_GET['semana'] ?? null;
        $anio = $_GET['anio'] ?? null;

        // Obtener datos cabecera del cliente
        $rutinaHead = $model->obtenerRutinaSemanal($userId, $semana, $anio);
        if (!$rutinaHead) {
            // Si no tiene rutina, mostrar vacío o redirect
            $this->view('rutinas.dia', ['dia' => $dia, 'semana' => 0, 'ejercicios' => [], 'admin_mode' => true]);
            return;
        }

        $ejercicios = $model->obtenerEjerciciosPorDia(
            $userId,
            $rutinaHead['semana'],
            $rutinaHead['anio'],
            $dia
        );

        foreach ($ejercicios as &$ej) {
            $ej['detalle_series'] = $model->obtenerSeries($ej['rutina_id']);
        }

        $this->view('rutinas.dia', [
            'dia' => $dia,
            'semana' => $rutinaHead['semana'],
            'anio' => $rutinaHead['anio'],
            'rutinaFull' => $rutinaHead,
            'ejercicios' => $ejercicios,
            'admin_mode' => true,
            'admin_mode_user_id' => $userId
        ]);
    }

    // --- AUTOASIGNACIÓN (SOLO SOCIOS) ---
    public function autoasignar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $usuarioId = $_SESSION['usuario_id'];
            $model = $this->model('RutinasModel');

            $ejercicio_id = $data['ejercicio_id'];
            $categoria_id = $data['categoria_id'];
            $dia_semana = $data['dia_semana'] ?? date('N'); // Hoy por defecto
            $series = (int) $data['series'];
            $reps = (int) $data['reps'];
            $peso = (float) $data['peso'];

            // Semana Actual
            $semana = date('W');
            $dyear = date('Y');
            $mes = date('n');

            $profesor_id = 13; // Default Admin/System

            $rutina_id = $model->insertarRutina(
                $usuarioId,
                $profesor_id,
                $categoria_id,
                $ejercicio_id,
                $dia_semana,
                $semana,
                $mes,
                $dyear
            );

            if ($rutina_id) {
                // Insertar Series
                for ($i = 0; $i < $series; $i++) {
                    $model->insertarSerie($rutina_id, $reps, $peso);
                }
                echo json_encode(['ok' => true]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }

    // --- AUTOASIGNACIÓN WIZARD (ALUMNOS) ---
    public function crear()
    {
        $rutinasModel = $this->model('RutinasModel');
        $ejerciciosAgrupados = $rutinasModel->listarEjerciciosAgrupados();

        $data = [
            'ejercicios' => $ejerciciosAgrupados,
            'ejerciciosJson' => json_encode($ejerciciosAgrupados)
        ];

        $this->view('rutinas.crear', $data);
    }

    public function guardar_wizard()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            $usuarioId = $_SESSION['usuario_id']; // El alumno se asigna a sí mismo
            $profesor_id = 13; // System/Admin default ID

            $rutinasModel = $this->model('RutinasModel');

            $datosExtras = [
                'dia_semana' => $input['dia_semana'],
                'nombre_semana' => $input['semana'], // Semana ISO actual o seleccionada
                'dyear' => date('Y')
            ];

            // Calcular mes
            $dto = new DateTime();
            $dto->setISODate($datosExtras['dyear'], $datosExtras['nombre_semana']);
            $datosExtras['mes'] = $dto->format('n');

            $ejercicios = $input['ejercicios']; // Array de ejercicios seleccionados

            // Reutilizamos la lógica del modelo
            $res = $rutinasModel->asignarPlantillaAAlumno($usuarioId, $profesor_id, $ejercicios, $datosExtras);

            if ($res) {
                // Replicación
                if (!empty($input['replicar_anio'])) {
                    $semana_inicio = (int) $datosExtras['nombre_semana'];
                    $anio = (int) $datosExtras['dyear'];
                    $weeksInYear = (int) date('W', mktime(0, 0, 0, 12, 28, $anio));

                    for ($s = $semana_inicio + 1; $s <= $weeksInYear; $s++) {
                        $clonedExtras = $datosExtras;
                        $clonedExtras['nombre_semana'] = $s;
                        $dto->setISODate($anio, $s);
                        $clonedExtras['mes'] = $dto->format('n');
                        $rutinasModel->asignarPlantillaAAlumno($usuarioId, $profesor_id, $ejercicios, $clonedExtras);
                    }
                }
                echo json_encode(['ok' => true, 'mensaje' => 'Rutina guardada correctamente']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar']);
            }
        }
    }

    public function api_ejercicios()
    {
        $model = $this->model('RutinasModel');
        $ejercicios = $model->listarEjerciciosAgrupados();
        header('Content-Type: application/json');
        echo json_encode($ejercicios);
    }

    public function api_detalle_plantilla($id)
    {
        header('Content-Type: application/json');
        $model = $this->model('RutinasModel');
        $detalle = $model->obtenerDetallePlantilla($id);
        echo json_encode($detalle);
    }

    public function plantillas()
    {
        if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] > 4) {
            $this->redirect('dashboard');
        }

        $model = $this->model('RutinasModel');
        $ejerciciosAgrupados = $model->listarEjerciciosAgrupados();
        $plantillas = $model->listarPlantillas($_SESSION['usuario_id']);

        $this->view('rutinas.plantillas', [
            'ejercicios' => $ejerciciosAgrupados,
            'ejerciciosJson' => json_encode($ejerciciosAgrupados),
            'plantillas' => $plantillas
        ]);
    }

    public function guardar_plantilla()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] > 4) {
                echo json_encode(['ok' => false, 'mensaje' => 'No autorizado']);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $nombre = $data['nombre'] ?? '';
            $ejercicios = $data['ejercicios'] ?? [];

            if (empty($nombre) || empty($ejercicios)) {
                echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos']);
                exit;
            }

            $model = $this->model('RutinasModel');
            $id = $model->guardarPlantilla($nombre, $_SESSION['usuario_id'], $ejercicios);

            echo json_encode(['ok' => !!$id, 'id' => $id]);
        }
    }

    public function eliminar_plantilla()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] > 4) {
                echo json_encode(['ok' => false, 'mensaje' => 'No autorizado']);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id) {
                $model = $this->model('RutinasModel');
                $res = $model->eliminarPlantilla($id);
                echo json_encode(['ok' => $res]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }
}
