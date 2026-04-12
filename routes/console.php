<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Processa tarefas agendadas (automações com delay) a cada minuto
Schedule::command('automations:process')->everyMinute()->withoutOverlapping();

// Processa a fila database (fallback para jobs que não foram executados via dispatchSync)
// Mesmo padrão do projeto de referência (script/restaurante) que funciona em produção na Hostinger
Schedule::command('queue:work database --tries=3 --stop-when-empty')->everyMinute()->withoutOverlapping();