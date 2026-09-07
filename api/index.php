<?php

try {
    // Paksa Laravel menulis semua file sementara ke folder /tmp bawaan Vercel
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp';
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
    $_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
    
    // REDIRECT SELURUH STORAGE (LOGS, SESSIONS, CACHE) KE /tmp
    $_ENV['APP_STORAGE_PATH'] = '/tmp/storage';

    // VERCEL seringkali meng-inject environment variable yang kosong ("") 
    // alih-alih tidak meng-setnya. Hal ini menyebabkan env('DRIVER', 'default') 
    // mengembalikan "" dan menyebabkan ArgumentCountError di Laravel.
    // Solusi: Hapus semua variabel lingkungan yang kosong agar Laravel menggunakan default-nya.
    foreach ($_ENV as $key => $value) {
        if ($value === '') {
            unset($_ENV[$key], $_SERVER[$key]);
            putenv($key); // Menghapus dari getenv()
        }
    }

    // Buat folder temporary di Vercel agar tidak permission error saat Laravel booting
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

    // Panggil index utama Laravel
    require __DIR__ . '/../public/index.php';

} catch (\ArgumentCountError $e) {
    http_response_code(500);
    echo "<h1>ArgumentCountError Debugging</h1>";
    echo "<p>It seems a config driver is evaluating to an empty string.</p>";
    $app = require __DIR__.'/../bootstrap/app.php';
    echo "<pre>";
    echo "session.driver = " . var_export($app->make('config')->get('session.driver'), true) . "\n";
    echo "app.maintenance.driver = " . var_export($app->make('config')->get('app.maintenance.driver'), true) . "\n";
    echo "hashing.driver = " . var_export($app->make('config')->get('hashing.driver'), true) . "\n";
    echo "cache.default = " . var_export($app->make('config')->get('cache.default'), true) . "\n";
    echo "logging.default = " . var_export($app->make('config')->get('logging.default'), true) . "\n";
    echo "database.default = " . var_export($app->make('config')->get('database.default'), true) . "\n";
    echo "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Vercel Serverless Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

