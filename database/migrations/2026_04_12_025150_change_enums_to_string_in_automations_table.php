<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            // Change enum columns to string to support dynamic values
            $table->string('trigger')->change();
            $table->string('source')->default('any')->change();
            $table->string('action')->default('grant_access')->change();
        });
    }

    public function down(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            $table->enum('trigger', ['purchase_approved', 'purchase_cancelled', 'purchase_refunded', 'purchase_expired'])->change();
            $table->enum('source', ['hotmart', 'cakto', 'wikify', 'any'])->default('any')->change();
            $table->enum('action', ['grant_access', 'revoke_access', 'send_email'])->default('grant_access')->change();
        });
    }
};
