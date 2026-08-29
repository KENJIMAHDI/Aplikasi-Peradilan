<?php

use Illuminate\Http\Request;

// 1. Setup writable storage & cache paths for Vercel Serverless environment
$storagePath = '/tmp/storage';
$_ENV['APP_STORAGE_PATH'] = $storagePath;
putenv("APP_STORAGE_PATH={$storagePath}");

$dirs = [
    $storagePath . '/app/public',
    $storagePath . '/framework/views',
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

// 3. Register Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 4. Bootstrap Laravel Application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 5. Handle incoming HTTP Request
$app->handleRequest(Request::capture());
