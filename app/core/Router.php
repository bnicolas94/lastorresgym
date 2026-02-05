<?php
class Router
{
    private $controller = 'AuthController'; // Controlador por defecto (Login)
    private $method = 'index';
    private $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1. Verificar Controlador
        if (isset($url[0])) {
            // Capitalizar primera letra (ej: home -> HomeController)
            $urlController = ucfirst($url[0]) . 'Controller';

            if (file_exists('../app/controllers/' . $urlController . '.php')) {
                $this->controller = $urlController;
                unset($url[0]);
            }
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Verificar Método
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Parámetros restantes
        $this->params = $url ? array_values($url) : [];

        // Ejecutar
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            // Limpiar y dividir URL
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
?>