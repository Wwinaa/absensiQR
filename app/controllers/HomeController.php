<?php
/**
 * Home Controller
 * Default controller loaded by the App router
 */
class HomeController extends Controller {
    
    public function index() {
        $data = [
            'title' => 'Beranda - SMK NU WARGABINANGUN',
            'description' => 'Selamat datang di Sistem Informasi Absensi SMK NU WARGABINANGUN Berbasis QR Code.'
        ];
        
        // Pass data to view and wrap it in the main layout
        $this->view('home/index', $data);
    }
    
    /**
     * Helper to check DB connection for debugging
     */
    public function check_db() {
        $db = new Database();
        if ($db->isConnected()) {
            echo "Database Connection: SUCCESS";
        } else {
            echo "Database Connection: FAILED. Check your .env file.";
        }
    }
}
