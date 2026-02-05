<?php
class AuthController extends Controller
{
    public function index()
    {
        // 1. Si ya estamos logueados, al dashboard
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }

        // 2. Si no, verificar cookie "remember_me"
        if (isset($_COOKIE['remember_me'])) {
            $this->checkAutoLogin();
        }

        $this->view('auth.login');
    }

    private function checkAutoLogin()
    {
        $token = $_COOKIE['remember_me'];
        $tokenHash = hash('sha256', $token);

        $usuarioModel = $this->model('Usuario');
        $user = $usuarioModel->obtenerUsuarioPorTokenHash($tokenHash);

        if ($user) {
            // Token válido -> Iniciar Sesión (mismo código que login)
            $this->iniciarSesion($user);
            $this->redirect('dashboard');
        } else {
            // Token inválido (quizás expiró o fue revocado) -> Limpiar cookie
            setcookie('remember_me', '', time() - 3600, "/");
        }
    }

    private function iniciarSesion($user)
    {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        $_SESSION['usuario_rol'] = $user['rol'];
        $_SESSION['usuario_rol_id'] = $user['rol_id'];
        $_SESSION['usuario_foto'] = $user['foto'];

        // Actualizar última actividad
        $usuarioModel = $this->model('Usuario');
        $usuarioModel->actualizarUltimoIngreso($user['id']);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = $_POST['dni'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']); // Checkbox

            if (empty($dni) || empty($password)) {
                $this->view('auth.login', ['error' => 'Por favor, completá todos los campos.']);
                return;
            }

            // Usar Modelo Usuario
            $usuarioModel = $this->model('Usuario');
            $user = $usuarioModel->obtenerPorDni($dni);

            if ($user && password_verify($password, $user['password'])) {
                // Login Exitoso
                $this->iniciarSesion($user);

                // Manejo de "Recordar Usuario"
                if ($remember) {
                    $token = bin2hex(random_bytes(32)); // 64 caracteres
                    $tokenHash = hash('sha256', $token);
                    // 30 días de expiración
                    $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);

                    if ($usuarioModel->guardarToken($user['id'], $tokenHash, $expiry)) {
                        // Cookie segura, HTTP Only
                        setcookie('remember_me', $token, time() + 60 * 60 * 24 * 30, "/", "", false, true);
                    }
                }

                $this->redirect('dashboard');
            } else {
                // Fallo
                $this->view('auth.login', ['error' => 'Credenciales incorrectas. Revisá tu DNI o contraseña.']);
            }
        } else {
            // Si intentan entrar por GET a /login, mandarlos al form
            $this->redirect('auth');
        }
    }

    public function logout()
    {
        // Eliminar token de BD si existe cookie
        if (isset($_COOKIE['remember_me'])) {
            $usuarioModel = $this->model('Usuario');
            $tokenHash = hash('sha256', $_COOKIE['remember_me']);
            $usuarioModel->eliminarToken($tokenHash);

            // Borrar cookie
            setcookie('remember_me', '', time() - 3600, "/");
        }

        session_destroy();
        $this->redirect('auth');
    }
}
?>