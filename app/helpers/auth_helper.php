<?php
/**
 * Authentication Helper Functions
 */

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('require_login')) {
    function require_login() {
        if (!is_logged_in()) {
            $_SESSION['error_message'] = "Silakan login terlebih dahulu untuk mengakses halaman ini.";
            redirect('auth');
            exit;
        }

        // Check Session Timeout (Default 30 minutes = 1800 seconds)
        $timeout_duration = 1800;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
            // Session expired
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['error_message'] = "Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.";
            redirect('auth');
            exit;
        }

        // Update last activity time stamp
        $_SESSION['last_activity'] = time();
    }
}

if (!function_exists('require_role')) {
    function require_role($roles = []) {
        // Ensure user is logged in first
        require_login();

        $userRole = $_SESSION['user_role'] ?? '';
        if ($userRole === 'kepsek') $userRole = 'kepala_sekolah';

        $normalizedRoles = array_map(function($r) {
            return ($r === 'kepsek') ? 'kepala_sekolah' : $r;
        }, $roles);

        if (!in_array($userRole, $normalizedRoles)) {
            // If user doesn't have the role, redirect to 403 forbidden page
            redirect('error/forbidden');
            exit;
        }
    }
}

if (!function_exists('redirect_if_logged_in')) {
    function redirect_if_logged_in() {
        if (is_logged_in()) {
            $role = $_SESSION['user_role'] ?? '';
            if (empty($role)) {
                redirect('error/forbidden');
            } else {
                if ($role === 'kepsek') $role = 'kepala_sekolah';
                redirect('dashboard/' . $role);
            }
            exit;
        }
    }
}

if (!function_exists('get_user_avatar')) {
    function get_user_avatar($userId = null) {
        if (!$userId && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }
        if (!$userId) return null;
        $exts = ['jpg', 'jpeg', 'png', 'webp'];
        foreach ($exts as $ext) {
            $path = "public/uploads/profiles/profile_{$userId}.{$ext}";
            if (file_exists($path)) {
                return base_url($path . '?v=' . filemtime($path));
            }
        }
        return null;
    }
}
