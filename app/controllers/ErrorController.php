<?php
/**
 * Error Controller
 * Handles generic application errors (404, 403, etc)
 */
class ErrorController extends Controller {
    public function forbidden() {
        http_response_code(403);
        $data = [
            'title' => '403 Forbidden - Akses Ditolak'
        ];
        $this->view('errors/403', $data);
    }

    public function not_found() {
        http_response_code(404);
        $data = [
            'title' => '404 Not Found'
        ];
        echo "404 - Halaman Tidak Ditemukan";
        // $this->view('errors/404', $data);
    }
}
