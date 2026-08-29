<?php

// Fix Vercel Serverless Read-Only Storage
$storageFolders = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageFolders as $folder) {
    if (!is_dir($folder)) {
        @mkdir($folder, 0777, true);
    }
}

// Override Storage & Cache Paths to /tmp
$_ENV['APP_STORAGE_PATH'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['SESSION_DRIVER'] = 'cookie';

// Forward request to Laravel
require __DIR__ . '/../public/index.php';
