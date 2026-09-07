<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $app['config']->set("hashing.driver", "");
    $app->make('hash')->driver();
} catch (Throwable $e) {
    echo get_class($e) . ': ' . $e->getMessage() . ' on line ' . $e->getLine() . "\n";
}
