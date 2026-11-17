<?php
/**
 * Base Controller
 * Handles loading models and views
 */
class Controller {

    /**
     * Load a View
     * @param string $view View path (e.g., 'home/index')
     * @param array $data Data to pass to the view
     */
    public function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            // Load the main layout, and inside it we will include $viewPath
            require_once __DIR__ . '/../views/layouts/main.php';
        } else {
            die("View does not exist: {$view}");
        }
    }

    /**
     * Load a View Standalone (without the main layout)
     * Useful for printable documents, PDFs, clean exports, etc.
     * @param string $view View path (e.g., 'admin/export_pdf')
     * @param array $data Data to pass to the view
     */
    public function viewStandalone($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View does not exist: {$view}");
        }
    }

    /**
     * Load a Model
     * @param string $model Model class name (e.g., 'User')
     * @return object Instantiated model object
     */
    public function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
    }

    /**
     * Helper to check if request is POST
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Helper to redirect
     */
    public function redirect($url) {
        redirect($url);
    }

    /**
     * Helper to set flash message
     */
    public function flash($type, $message) {
        set_flash($message, $type);
    }
}
