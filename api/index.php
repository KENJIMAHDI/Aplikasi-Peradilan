<?php

// Paksa Laravel menulis semua file sementara ke folder /tmp bawaan Vercel
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';

// Panggil index utama Laravel
require __DIR__ . '/../public/index.php';
