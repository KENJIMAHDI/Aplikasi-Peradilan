<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */

if (isset($_GET['debug_env'])) {
    echo "<pre><h1>ENV DUMP</h1>\n";
    $safe_env = $_ENV;
    $safe_server = $_SERVER;
    // Hide sensitive keys
    foreach(['APP_KEY', 'DB_PASSWORD'] as $key) {
        if(isset($safe_env[$key])) $safe_env[$key] = '***';
        if(isset($safe_server[$key])) $safe_server[$key] = '***';
    }
    echo "ENV:\n";
    print_r($safe_env);
    echo "\nSERVER:\n";
    print_r($safe_server);
    echo "</pre>";
    exit;
}

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
