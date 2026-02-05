<?php

class AnunciosController extends Controller
{
    private $anunciosModel;

    public function __construct()
    {
        // Verificar sesión
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->anunciosModel = $this->model('AnunciosModel');

        // Fallback por si no se cargó el rol_id en sesión
        if (!isset($_SESSION['usuario_rol_id'])) {
            $userModel = $this->model('Usuario');
            $u = $userModel->obtenerPorId($_SESSION['usuario_id']);
            $_SESSION['usuario_rol_id'] = $u['rol_id'] ?? 5; // Socio por defecto
        }
    }

    /**
     * Vista de gestión de anuncios (Solo Admin/Profesores)
     */
    public function gestion()
    {
        // Verificar permisos (Asumimos rol <= 4 para profesores/admin)
        if ($_SESSION['usuario_rol_id'] > 4) {
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $anuncios = $this->anunciosModel->obtenerTodos();

        $data = [
            'titulo' => 'Gestión de Anuncios',
            'anuncios' => $anuncios
        ];

        $this->view('anuncios/gestion', $data);
    }

    /**
     * Guardar o actualizar anuncio (AJAX/POST)
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $_POST['id'] ?? null,
                'titulo' => $_POST['titulo'] ?? '',
                'contenido' => $_POST['contenido'] ?? '',
                'prioridad' => $_POST['prioridad'] ?? 'baja',
                'fecha_expiracion' => $_POST['fecha_expiracion'] ?? null,
                'imagen_url' => $_POST['imagen_url'] ?? null
            ];

            if ($this->anunciosModel->guardar($data)) {
                echo json_encode(['ok' => true]);
            } else {
                echo json_encode(['ok' => false, 'error' => 'Error al guardar']);
            }
        }
    }

    /**
     * Eliminar anuncio (AJAX/POST)
     */
    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id && $this->anunciosModel->eliminar($id)) {
                echo json_encode(['ok' => true]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }

    /**
     * Alternar estado activo (AJAX/POST)
     */
    public function toggle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? 0;

            if ($id && $this->anunciosModel->toggleActivo($id)) {
                echo json_encode(['ok' => true]);
            } else {
                echo json_encode(['ok' => false]);
            }
        }
    }
}
