<?php
class Router
{
    private $controller = 'LoginController';
    private $method = 'index';
    private $params = [];

    public function __construct($url)
    {
        // Limpiar y separar la URL
        $url = $this->parseUrl($url);

        // Verificar si existe el controlador
        if (!empty($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = __DIR__ . '/../controller/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        require_once __DIR__ . '/../controller/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Verificar si existe el método
        if (isset($url[1])) {
            $methodName = $url[1];
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            }
        }

        // Parámetros restantes
        $this->params = $url ? array_values($url) : [];
    }

    public function run()
    {
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl($url)
    {
        if (isset($url)) {
            return explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}