<?php
class PerfilController extends Controller
{

    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth');
        }
    }

    public function index()
    {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->obtenerPorId($_SESSION['usuario_id']);

        $this->view('perfil/index', ['usuario' => $usuario]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioModel = $this->model('Usuario');

            $datos = [
                'telefono' => $_POST['telefono'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? ''
            ];

            // 1. Actualizar Info Básica
            $usuarioModel->actualizarPerfil($_SESSION['usuario_id'], $datos);

            // 2. Manejo de Foto de Perfil
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                // Config
                $uploadDir = dirname(__DIR__, 2) . '/public/img/foto-perfil/';

                // Crear carpeta si no existe
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileTmpPath = $_FILES['foto']['tmp_name'];
                $fileName = $_FILES['foto']['name'];
                $fileSize = $_FILES['foto']['size'];
                $fileType = $_FILES['foto']['type'];

                // Validar extensión
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg', 'webp'];

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    // Generar nombre único: perfil_{ID}_{TIMESTAMP}.ext
                    $newFileName = 'perfil_' . $_SESSION['usuario_id'] . '_' . time() . '.' . $fileExtension;
                    $dest_path = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Guardar SOLO el nombre del archivo en BD
                        // (La vista se encargará de concatenar la ruta)
                        $usuarioModel->actualizarFoto($_SESSION['usuario_id'], $newFileName);

                        // Actualizar sesión para reflejo inmediato (opcional, aunque la vista tira de BD o Session)
                        // Para consistencia con la vista que concatenará, guardamos el nombre en sesión también
                        // OJO: Si las vistas usan session foto, debemos actualizar esto.
                        // PERO: Las vistas actuales esperan URL completa o nombre? 
                        // El cambio implica que ahora 'foto' puede ser solo nombre. 
                        // Actualizaré la sesión con la RUTA FINAL construida para no romper lógica antigua temporalmente
                        // O mejor, guardamos el nombre plano y ajustamos todas las vistas ahora.
                        $_SESSION['usuario_foto'] = BASE_URL . 'img/foto-perfil/' . $newFileName;

                        // NOTA: Para respetar el pedido estricto, en BD va $newFileName. 
                        // En sesión guardo la URL completa para facilitar visualización inmediata sin cambiar TODAS las vistas ya mismo?
                        // No, el plan dice actualizar vistas. Así que guardaré en sesión lo que corresponda.
                        // Si actualizo las vistas para leer concatenado, entonces en sesión debería estar concatenado?
                        // El layout usa $_SESSION['usuario_foto']. Si lo cambio a filename, el layout se rompre hasta que lo arregle.
                        // Arreglaré el layout en el siguiente paso.
                    }
                }
            }

            // Redireccionar con éxito
            $this->redirect('perfil?status=saved');
        }
    }

    public function password()
    {
        $this->view('perfil/password');
    }

    public function guardar_password()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actual = $_POST['password_actual'] ?? '';
            $nueva = $_POST['password_nueva'] ?? '';
            $confirmar = $_POST['password_confirmar'] ?? '';

            $errores = [];

            if (empty($actual) || empty($nueva) || empty($confirmar)) {
                $errores[] = "Todos los campos son obligatorios.";
            }

            if ($nueva !== $confirmar) {
                $errores[] = "Las contraseñas nuevas no coinciden.";
            }

            if (strlen($nueva) < 6) {
                $errores[] = "La contraseña nueva debe tener al menos 6 caracteres.";
            }

            $usuarioModel = $this->model('Usuario');

            // Verificar actual
            if (empty($errores) && !$usuarioModel->verificarPassword($_SESSION['usuario_id'], $actual)) {
                $errores[] = "La contraseña actual es incorrecta.";
            }

            // Si hay errores, volver a mostrar form con errores
            if (!empty($errores)) {
                $this->view('perfil/password', ['errores' => $errores]);
                return;
            }

            // Guardar
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            if ($usuarioModel->actualizarPassword($_SESSION['usuario_id'], $hash)) {
                $this->redirect('perfil?status=password_updated');
            } else {
                $this->view('perfil/password', ['errores' => ['Error al actualizar en base de datos.']]);
            }
        }
    }
}
?>