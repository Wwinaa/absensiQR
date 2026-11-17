<?php
/**
 * URL Helper Functions
 */

if (!function_exists('base_url')) {
    /**
     * Get the base URL of the application
     * @param string $path Optional path to append
     * @return string
     */
    function base_url($path = '') {
        $baseUrl = defined('APP_URL') ? APP_URL : 'http://localhost/absen';
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a specific URL
     * @param string $path
     */
    function redirect($path) {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (!empty($_POST['ajax']));
        if ($isAjax && strpos($path, 'absensi/scan') !== false) {
            header('Content-Type: application/json');
            $scanRes = $_SESSION['scan_result'] ?? null;
            $flashSuccess = $_SESSION['success_message'] ?? null;
            $flashError = $_SESSION['error_message'] ?? null;
            $flashWarning = $_SESSION['warning_message'] ?? null;
            $flashInfo = $_SESSION['info_message'] ?? null;

            $status = !empty($flashError) ? 'error' : (!empty($flashWarning) ? 'warning' : 'success');
            $message = $flashError ?: ($flashWarning ?: ($flashSuccess ?: ($flashInfo ?: '')));

            echo json_encode([
                'success' => ($status === 'success' || ($scanRes && empty($scanRes['is_sudah_absen']))),
                'status' => $status,
                'message' => $message,
                'scan_result' => $scanRes
            ]);
            exit;
        }

        header('Location: ' . base_url($path));
        exit;
    }
}
