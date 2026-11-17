<?php
/**
 * Main Entry Point (Front Controller)
 */

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions that might be needed globally early on
require_once __DIR__ . '/app/helpers/url_helper.php';
require_once __DIR__ . '/app/helpers/auth_helper.php';
require_once __DIR__ . '/app/helpers/flash_helper.php';

// Load Environment Variables (Simple .env parser)
$envFilePath = __DIR__ . '/.env';
if (file_exists($envFilePath)) {
    $envVariables = parse_ini_file($envFilePath);
    foreach ($envVariables as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Load configurations
require_once __DIR__ . '/app/config/config.php';

// Autoload Core Libraries
spl_autoload_register(function($className) {
    // Directories to check for classes
    $directories = [
        __DIR__ . '/app/core/',
        __DIR__ . '/app/models/',
        __DIR__ . '/app/controllers/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return; // Stop searching once found
        }
    }
});

// Init App (Router)
$app = new App();
