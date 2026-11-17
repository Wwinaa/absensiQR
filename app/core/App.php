<?php
/**
 * Simple Router (Front Controller Core)
 * Parses URL and loads appropriate Controller and Method
 */
class App {
    protected $controller = 'AuthController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Check Controller
        if (isset($url[0])) {
            // Capitalize first letter and handle hyphens/underscores (e.g. wali-kelas or wali_kelas -> WaliKelasController)
            $controllerName = str_replace(['-', '_'], ' ', $url[0]);
            $controllerName = ucwords($controllerName);
            $controllerName = str_replace(' ', '', $controllerName) . 'Controller';
            
            if (file_exists(__DIR__ . '/../controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Require and instantiate controller
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Check Method
        if (isset($url[1])) {
            $methodName = str_replace('-', '_', $url[1]);
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            }
        }

        // 3. Get Params
        $this->params = $url ? array_values($url) : [];

        // Call the method with params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
