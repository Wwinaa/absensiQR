<?php
/**
 * Configuration File
 * Loads configurations from environment variables
 */

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'db_absensi_smk');

define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/absen');
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Sistem Absensi SMK');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');

// Set Default Timezone
date_default_timezone_set('Asia/Jakarta');

// Telegram Bot Configuration
define('TELEGRAM_CHAT_ID', $_ENV['TELEGRAM_CHAT_ID'] ?? '6634167495');
define('TELEGRAM_BOT_TOKEN', $_ENV['TELEGRAM_BOT_TOKEN'] ?? '8754245306:AAEmHuO4fK0rMg71hUQr_XqDgkY3f_95pUk');
