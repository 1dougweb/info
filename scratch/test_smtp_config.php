<?php

// Script para rodar via terminal: php scratch/test_smtp_config.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Mail\DynamicMailService;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

echo "--- TESTE DE CONFIGURAÇÃO DE E-MAIL ---\n";

// 1. Ver estado inicial (do .env)
echo "Mailer padrão inicial (.env): " . Config::get('mail.default') . "\n";

// 2. Aplicar configurações dinâmicas
echo "Aplicando configurações dinâmicas...\n";
DynamicMailService::applySettings();

// 3. Verificar se mudou para SMTP
$finalMailer = Config::get('mail.default');
echo "Mailer padrão FINAL: " . $finalMailer . "\n";

if ($finalMailer === 'smtp') {
    echo "✅ SUCESSO: O driver SMTP foi forçado com sucesso.\n";
} else {
    echo "❌ FALHA: O driver continua sendo " . $finalMailer . ".\n";
}

// 4. Verificar host carregado
echo "Host configurado: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "Porta configurada: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "--- FIM DO TESTE ---";
