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
$app = require_once __DIR__.'/../bootstrap/app.php';

echo "<pre>";
echo "session.driver = " . var_export($app->make('config')->get('session.driver'), true) . "\n";
echo "app.maintenance.driver = " . var_export($app->make('config')->get('app.maintenance.driver'), true) . "\n";
echo "hashing.driver = " . var_export($app->make('config')->get('hashing.driver'), true) . "\n";
echo "cache.default = " . var_export($app->make('config')->get('cache.default'), true) . "\n";
echo "logging.default = " . var_export($app->make('config')->get('logging.default'), true) . "\n";
echo "database.default = " . var_export($app->make('config')->get('database.default'), true) . "\n";
echo "</pre>";
exit;

$app->handleRequest(Request::capture());
