<?php
class AsignacionController extends Controller
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

    public function index($alumnoId = null)
    {
        if (!$alumnoId) {
            $this->redirect('alumnos');
        }

        $rutinasModel = $this->model('RutinasModel');
        $usuarioModel = $this->model('Usuario');

        $alumno = $usuarioModel->obtenerPorId($alumnoId);
        if (!$alumno) {
            die("Alumno no encontrado");
        }

        $ejerciciosAgrupados = $rutinasModel->listarEjerciciosAgrupados();
        $plantillas = $rutinasModel->listarPlantillas($_SESSION['usuario_id']);

        $data = [
            'alumno' => $alumno,
            'ejercicios' => $ejerciciosAgrupados,
            'plantillas' => $plantillas,
            'ejerciciosJson' => json_encode($ejerciciosAgrupados),
            'plantillasJson' => json_encode($plantillas)
        ];

        $this->view('admin.asignar', $data);
    }

    public function guardar()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $alumno_id = $input['alumno_id'] ?? null;
        $tipo = $input['tipo'] ?? 'manual';

        if (!$alumno_id) {
            echo json_encode(['ok' => false, 'mensaje' => 'Falta ID alumno']);
            exit;
        }

        $rutinasModel = $this->model('RutinasModel');
        $profesor_id = $_SESSION['usuario_id'];

        $datosExtras = [
            'dia_semana' => $input['dia_semana'],
            'nombre_semana' => $input['semana'],
            'mes' => date('n'),
            'dyear' => date('Y')
        ];

        $dto = new DateTime();
        $dto->setISODate($datosExtras['dyear'], $datosExtras['nombre_semana']);
        $datosExtras['mes'] = $dto->format('n');

        if ($tipo === 'plantilla') {
            $ejercicios = $input['ejercicios'];
            $res = $rutinasModel->asignarPlantillaAAlumno($alumno_id, $profesor_id, $ejercicios, $datosExtras);
        } else {
            $ejercicios = $input['ejercicios'];
            $res = $rutinasModel->asignarPlantillaAAlumno($alumno_id, $profesor_id, $ejercicios, $datosExtras);
        }

        if ($res) {
            if (!empty($input['replicar_anio'])) {
                $semana_inicio = (int) $datosExtras['nombre_semana'];
                $anio = (int) $datosExtras['dyear'];
                $weeksInYear = (int) date('W', mktime(0, 0, 0, 12, 28, $anio));

                for ($s = $semana_inicio + 1; $s <= $weeksInYear; $s++) {
                    $clonedExtras = $datosExtras;
                    $clonedExtras['nombre_semana'] = $s;
                    $dto = new DateTime();
                    $dto->setISODate($anio, $s);
                    $clonedExtras['mes'] = $dto->format('n');
                    $rutinasModel->asignarPlantillaAAlumno($alumno_id, $profesor_id, $ejercicios, $clonedExtras);
                }
            }

            echo json_encode(['ok' => true, 'mensaje' => 'Asignado correctamente' . ($input['replicar_anio'] ? ' (y replicado)' : '')]);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar']);
        }
    }

    public function api_detalle_plantilla()
    {
        $id = $_GET['id'] ?? 0;
        $rutinasModel = $this->model('RutinasModel');
        $detalle = $rutinasModel->obtenerDetallePlantilla($id);

        header('Content-Type: application/json');
        echo json_encode($detalle);
    }
}
?>