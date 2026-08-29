<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Illuminate\Http\Request;

try {
    // 1. Setup writable storage & compiled view paths for Vercel Serverless
    $storagePath = '/tmp/storage';
    $compiledViews = '/tmp/storage/framework/views';

    $_ENV['APP_STORAGE_PATH'] = $storagePath;
    $_ENV['VIEW_COMPILED_PATH'] = $compiledViews;

    putenv("APP_STORAGE_PATH={$storagePath}");
    putenv("VIEW_COMPILED_PATH={$compiledViews}");

    $dirs = [
        $storagePath . '/app/public',
        $compiledViews,
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/logs',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // 2. Set environment defaults & HTTPS scheme
    $_SERVER['HTTPS'] = 'on';
    putenv('HTTPS=on');

    // Ensure all Laravel driver managers receive valid non-empty driver defaults
    $driverDefaults = [
        'APP_MAINTENANCE_DRIVER' => 'file',
        'SESSION_DRIVER'        => 'cookie',
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

    // 3. Register Composer Autoloader
    $autoloader = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoloader)) {
        require $autoloader;
    } else {
        throw new \Exception("Vendor autoloader not found at {$autoloader}!");
    }

    // 4. Bootstrap Laravel Application
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // 5. Handle incoming HTTP Request
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(200);
    echo "<div style='font-family:sans-serif; padding:20px; background:#fff0f0; border:2px solid #e53e3e; border-radius:10px; margin:20px;'>";
    echo "<h2 style='color:#c53030; margin-top:0;'>⚠️ Detail Error di Vercel:</h2>";
    echo "<p style='font-size:16px;'><strong>Pesan Error:</strong> <span style='color:#9b2c2c;'>" . htmlspecialchars($e->getMessage()) . "</span></p>";
    echo "<p><strong>Lokasi File:</strong> <code>" . htmlspecialchars($e->getFile()) . "</code> (Baris " . $e->getLine() . ")</p>";
    
    if ($prev = $e->getPrevious()) {
        echo "<h3 style='color:#c53030;'>Penyebab Utama (Previous Error):</h3>";
        echo "<p style='font-size:16px;'><strong>Pesan:</strong> <span style='color:#9b2c2c;'>" . htmlspecialchars($prev->getMessage()) . "</span></p>";
        echo "<pre style='background:#1a202c; color:#fc8181; padding:15px; border-radius:6px; overflow:auto; max-height:250px; font-size:13px;'>" . htmlspecialchars($prev->getTraceAsString()) . "</pre>";
    }

    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#1a202c; color:#68d391; padding:15px; border-radius:6px; overflow:auto; max-height:350px; font-size:13px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
