<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Config Locale: " . config('app.locale') . "\n";
echo "Config Fallback: " . config('app.fallback_locale') . "\n";
echo "Carbon Locale: " . \Carbon\Carbon::getLocale() . "\n";
echo "Translator Locale: " . app('translator')->getLocale() . "\n";
echo "Sample diffForHumans: " . \Carbon\Carbon::now()->subHours(8)->diffForHumans() . "\n";
