<?php

/**
 * Diagnóstico de Cron para Laravel
 */

$artisan = realpath(__DIR__ . '/../artisan');
$php_path = exec('which php') ?: 'Não encontrado via which';
$php_version = PHP_VERSION;

echo "<h2>Ferramenta de Diagnóstico de Cron</h2>";
echo "<b>Versão do PHP atual (via Web):</b> $php_version <br>";
echo "<b>Caminho do executável PHP (provável):</b> $php_path <br>";
echo "<b>Caminho absoluto do Artisan:</b> $artisan <br>";

if ($artisan && file_exists($artisan)) {
    echo "<b style='color:green'>O arquivo artisan foi encontrado!</b><br>";
    echo "<b>Permissões do artisan:</b> " . substr(sprintf('%o', fileperms($artisan)), -4) . "<br>";
} else {
    echo "<b style='color:red'>ERRO: O arquivo artisan NÃO foi encontrado nesse caminho.</b><br>";
}

echo "<h3>Sugestão de Comando para o Cron:</h3>";
if ($artisan) {
    echo "<textarea style='width:100%; height:50px; background:#f4f4f4; padding:10px; border:1px solid #ccc;'>/opt/alt/php82/usr/bin/php $artisan schedule:run</textarea>";
}

echo "<h3>Teste de Execução Local:</h3>";
if ($artisan) {
    $output = shell_exec("/opt/alt/php82/usr/bin/php $artisan list automations 2>&1");
    echo "<b>Resultado do teste 'list automations':</b><pre style='background:#eee; padding:10px;'>$output</pre>";
}

echo "<br><br><a href='/admin/automations'>Voltar</a>";
