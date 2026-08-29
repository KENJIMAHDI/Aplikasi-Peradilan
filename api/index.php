<?php

// Buat direktori temporary yang dibutuhkan Laravel di Vercel
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

// Bind custom storage path
$_ENV['APP_STORAGE_PATH'] = '/tmp/storage';

// Require index.php bawaan Laravel
require __DIR__ . '/../public/index.php';
