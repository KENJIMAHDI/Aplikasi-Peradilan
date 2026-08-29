<?php

use Illuminate\Http\Request;

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
require __DIR__ . '/../vendor/autoload.php';

// 4. Bootstrap Laravel Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 5. Handle incoming HTTP Request
$app->handleRequest(Request::capture());
