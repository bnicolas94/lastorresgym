<?php
class View
{
    public static function render($view, $data = [])
    {
        // Extraer variables para que estén disponibles en la vista
        extract($data);

        // Convertir puntos en barras para estructuras de carpetas (ej: 'auth.login' -> 'auth/login')
        $viewFile = str_replace('.', '/', $view);

        $file = '../app/views/' . $viewFile . '.php';

        if (file_exists($file)) {
            require_once $file;
        } else {
            die("La vista '$viewFile' no existe.");
        }
    }
}
?>