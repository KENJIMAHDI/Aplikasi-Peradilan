<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Illuminate\Http\Request;

try {
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }

    // Setup writable /tmp storage directories for Vercel serverless environment
    $tmpStorage = '/tmp/storage';
    $dirs = [
        $tmpStorage . '/app/public',
        $tmpStorage . '/framework/views',
        $tmpStorage . '/framework/cache/data',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/logs',
        '/tmp/bootstrap/cache'
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // Override storage & compiled view paths for serverless
    $_ENV['APP_STORAGE_PATH'] = $tmpStorage;
    putenv("APP_STORAGE_PATH={$tmpStorage}");
    putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

    // Register Composer autoloader
    $autoloader = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoloader)) {
        require $autoloader;
    } else {
        throw new \Exception("Vendor autoloader not found at {$autoloader}! Ensure composer packages are committed or built.");
    }

    // Bootstrap Laravel and handle request
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(200); // Return 200 so Vercel displays the error details directly on screen
    echo "<div style='font-family:sans-serif; padding:20px; background:#fff0f0; border:2px solid #e53e3e; border-radius:10px; margin:20px;'>";
    echo "<h2 style='color:#c53030; margin-top:0;'>⚠️ Detail Error Laravel di Vercel:</h2>";
    echo "<p style='font-size:16px;'><strong>Pesan Error:</strong> <span style='color:#9b2c2c;'>" . htmlspecialchars($e->getMessage()) . "</span></p>";
    echo "<p><strong>Lokasi File:</strong> <code>" . htmlspecialchars($e->getFile()) . "</code> (Baris " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#1a202c; color:#68d391; padding:15px; border-radius:6px; overflow:auto; max-height:450px; font-size:13px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
