<?php
class Controller
{
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = [])
    {
        // En lugar de renderizar la vista directamente, preparamos variables
        // y renderizamos el layouts/main.php que a su vez incluye la vista

        if (strpos($view, 'auth') !== false) {
            // Si es auth, render directo sin layout principal (o con layout auth)
            View::render($view, $data);
        } else {
            // Pasamos $view como variable para que el layout sepa qué incluir
            $data['contentView'] = str_replace('.', '/', $view);
            View::render('layouts/main', $data);
        }
    }

    // Helper para redireccionar
    public function redirect($url)
    {
        header("Location: " . BASE_URL . $url);
        exit();
    }
}
?>