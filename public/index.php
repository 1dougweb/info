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

// Capture Webhook early for diagnosis of server blockage vs application logic
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/webhooks/custom/') !== false) {
    @file_put_contents(__DIR__.'/../storage/logs/raw_webhook.txt', date('Y-m-d H:i:s') . " - Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . " - Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'N/A') . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . PHP_EOL . "Payload: " . file_get_contents('php://input') . PHP_EOL . "------" . PHP_EOL, FILE_APPEND);
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
