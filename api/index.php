<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Buat folder temporary di Vercel agar tidak permission error
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Load Autoloader & Application (Format Laravel 11)
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Arahkan storage path ke temporary folder
$app->useStoragePath('/tmp/storage');

// 4. Jalankan Aplikasi (Laravel 11 Way)
$app->handleRequest(Request::capture());
