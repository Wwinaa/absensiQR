<?php
/**
 * SettingModel
 * Model untuk mengelola tabel pengaturan_sistem dan konfigurasi AI Chatbot
 */
class SettingModel extends BaseModel {

    public function __construct() {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS pengaturan_sistem (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) NOT NULL UNIQUE,
                setting_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();
        } catch (Exception $e) {
            // Abaikan jika tabel sudah ada
        }
    }

    public function get($key, $default = null) {
        $this->db->query("SELECT setting_value FROM pengaturan_sistem WHERE setting_key = :k LIMIT 1");
        $this->db->bind(':k', $key);
        $row = $this->db->single();
        return $row ? $row->setting_value : $default;
    }

    public function set($key, $value) {
        $this->db->query("INSERT INTO pengaturan_sistem (setting_key, setting_value) 
                          VALUES (:k, :v) 
                          ON DUPLICATE KEY UPDATE setting_value = :v2, updated_at = CURRENT_TIMESTAMP");
        $this->db->bind(':k', $key);
        $this->db->bind(':v', $value);
        $this->db->bind(':v2', $value);
        return $this->db->execute();
    }

    public function getChatbotConfig() {
        // Fallback default dari .env jika belum tersimpan di database
        $envKey = '';
        $envModel = 'nvidia/nemotron-3.5-bolt-e4b';
        $envUrl = 'https://integrate.api.nvidia.com/v1/chat/completions';

        $envFile = dirname(dirname(__DIR__)) . '/.env';
        if (file_exists($envFile)) {
            $parsed = @parse_ini_file($envFile);
            if (!empty($parsed['NVIDIA_API_KEY'])) $envKey = $parsed['NVIDIA_API_KEY'];
            if (!empty($parsed['NVIDIA_MODEL'])) $envModel = $parsed['NVIDIA_MODEL'];
            if (!empty($parsed['NVIDIA_API_URL'])) $envUrl = $parsed['NVIDIA_API_URL'];
        }

        $apiKey = $this->get('chatbot_api_key', $envKey);
        $model = $this->get('chatbot_model', $envModel);
        $apiUrl = $this->get('chatbot_api_url', $envUrl);

        return [
            'api_key' => $apiKey,
            'model' => $model,
            'api_url' => $apiUrl
        ];
    }

    public function saveChatbotConfig($apiKey, $model, $apiUrl = 'https://integrate.api.nvidia.com/v1/chat/completions') {
        $this->set('chatbot_api_key', trim($apiKey));
        $this->set('chatbot_model', trim($model));
        $this->set('chatbot_api_url', trim($apiUrl));

        // Sinkronisasi otomatis ke file .env jika ada
        $this->updateEnvFile([
            'NVIDIA_API_KEY' => trim($apiKey),
            'NVIDIA_MODEL' => trim($model)
        ]);

        return true;
    }

    private function updateEnvFile(array $data) {
        $envFile = dirname(dirname(__DIR__)) . '/.env';
        if (!file_exists($envFile) || !is_writable($envFile)) {
            return false;
        }

        $content = file_get_contents($envFile);
        foreach ($data as $key => $val) {
            $pattern = "/^" . preg_quote($key, '/') . "=.*$/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$val}", $content);
            } else {
                $content .= "\n{$key}={$val}";
            }
        }
        return @file_put_contents($envFile, $content) !== false;
    }
}
