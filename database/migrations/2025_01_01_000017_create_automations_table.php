<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('trigger', ['purchase_approved', 'purchase_cancelled', 'purchase_refunded', 'purchase_expired']);
            $table->enum('source', ['hotmart', 'cakto', 'wikify', 'any'])->default('any');
            $table->string('source_product_id')->nullable()->comment('External product ID from checkout platform');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('action', ['grant_access', 'revoke_access', 'send_email'])->default('grant_access');
            $table->json('action_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automations');
    }
};
