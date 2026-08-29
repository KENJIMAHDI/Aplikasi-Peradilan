<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Force APP_DEBUG=true in system environment before Laravel boots
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';
$_SERVER['HTTPS'] = 'on';
putenv('APP_DEBUG=true');
putenv('HTTPS=on');

// Ensure BCRYPT_ROUNDS is valid integer between 4 and 31 (default 12) to prevent password_hash ValueError
$bcryptRounds = (!empty($_ENV['BCRYPT_ROUNDS']) && is_numeric($_ENV['BCRYPT_ROUNDS']) && (int)$_ENV['BCRYPT_ROUNDS'] >= 4 && (int)$_ENV['BCRYPT_ROUNDS'] <= 31)
    ? (int)$_ENV['BCRYPT_ROUNDS']
    : 12;

$_ENV['BCRYPT_ROUNDS'] = $bcryptRounds;
$_SERVER['BCRYPT_ROUNDS'] = $bcryptRounds;
putenv("BCRYPT_ROUNDS={$bcryptRounds}");

// Ensure all Laravel driver managers receive valid non-empty driver defaults
// SESSION_DRIVER=database allows persistent sessions via Supabase sessions table
$driverDefaults = [
    'APP_MAINTENANCE_DRIVER' => 'file',
    'SESSION_DRIVER'        => 'database',
    'CACHE_STORE'           => 'array',
    'FILESYSTEM_DISK'       => 'local',
    'LOG_CHANNEL'           => 'stderr',
    'DB_CONNECTION'         => 'pgsql',
    'QUEUE_CONNECTION'       => 'sync',
    'BROADCAST_CONNECTION'   => 'log',
    'HASH_DRIVER'           => 'bcrypt',
];

foreach ($driverDefaults as $key => $fallback) {
    $val = (!empty($_ENV[$key]) && trim($_ENV[$key]) !== '') ? $_ENV[$key] : $fallback;
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
    putenv("{$key}={$val}");
}

use Illuminate\Http\Request;

try {
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }

    // Setup writable /tmp storage directories for Vercel serverless environment
    $tmpStorage = '/tmp/storage';
    $tmpBootstrapCache = '/tmp/bootstrap/cache';
    $dirs = [
        $tmpStorage . '/app/public',
        $tmpStorage . '/framework/views',
        $tmpStorage . '/framework/cache/data',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/logs',
        $tmpBootstrapCache
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // Override storage & cache paths for serverless environment
    $_ENV['APP_STORAGE_PATH'] = $tmpStorage;
    $_ENV['APP_SERVICES_CACHE'] = $tmpBootstrapCache . '/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = $tmpBootstrapCache . '/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = $tmpBootstrapCache . '/config.php';
    $_ENV['APP_ROUTES_CACHE'] = $tmpBootstrapCache . '/routes.php';
    $_ENV['APP_EVENTS_CACHE'] = $tmpBootstrapCache . '/events.php';

    putenv("APP_STORAGE_PATH={$tmpStorage}");
    putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

    // Remove any stale bootstrap cache files committed from Windows
    @unlink(__DIR__ . '/../bootstrap/cache/packages.php');
    @unlink(__DIR__ . '/../bootstrap/cache/services.php');
    @unlink(__DIR__ . '/../bootstrap/cache/config.php');
    @unlink(__DIR__ . '/../bootstrap/cache/routes.php');

    // Register Composer autoloader
    $autoloader = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoloader)) {
        require $autoloader;
    } else {
        throw new \Exception("Vendor autoloader not found at {$autoloader}!");
    }

    // Bootstrap Laravel and handle request
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(200);
    echo "<div style='font-family:sans-serif; padding:20px; background:#fff0f0; border:2px solid #e53e3e; border-radius:10px; margin:20px;'>";
    echo "<h2 style='color:#c53030; margin-top:0;'>⚠️ Detail Error Laravel di Vercel:</h2>";
    echo "<p style='font-size:16px;'><strong>Pesan Error:</strong> <span style='color:#9b2c2c;'>" . htmlspecialchars($e->getMessage()) . "</span></p>";
    echo "<p><strong>Lokasi File:</strong> <code>" . htmlspecialchars($e->getFile()) . "</code> (Baris " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#1a202c; color:#68d391; padding:15px; border-radius:6px; overflow:auto; max-height:450px; font-size:13px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
