<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Garante que a coluna `source` seja TEXT em produção.
 * A migration anterior (2026_04_12_015840) pode não ter rodado corretamente
 * em produção se o Hostinger ainda tinha o ENUM original com restrição de tamanho.
 * Esta migration usa changeColumn com TEXT explícito para remover qualquer restrição.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            // TEXT suporta até 65.535 bytes — sem restrição de tamanho para UUIDs do tipo custom_<uuid>
            $table->text('source')->change();
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->string('source')->change();
        });
    }
};
