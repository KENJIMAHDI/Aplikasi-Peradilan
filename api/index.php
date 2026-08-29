<?php

use Illuminate\Http\Request;

// Set Session & App URL Overrides for Vercel
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['SESSION_SECURE_COOKIE'] = 'true';
$_ENV['APP_URL'] = 'https://aplikasi-peradilan.vercel.app';

putenv('SESSION_DRIVER=cookie');
putenv('SESSION_SECURE_COOKIE=true');
putenv('APP_URL=https://aplikasi-peradilan.vercel.app');
putenv('HTTPS=on');
$_SERVER['HTTPS'] = 'on';

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Atur direktori storage temporary di Vercel
$app->useStoragePath('/tmp/storage');

$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs'
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Forward Vercel request to Laravel
$app->handleRequest(Request::capture());
