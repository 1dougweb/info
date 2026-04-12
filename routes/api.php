<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/hotmart', [WebhookController::class, 'hotmart'])->name('webhooks.hotmart');
Route::post('/webhooks/cakto', [WebhookController::class, 'cakto'])->name('webhooks.cakto');
Route::post('/webhooks/wikify', [WebhookController::class, 'wikify'])->name('webhooks.wikify');

Route::post('/webhooks/custom/{uuid}', [WebhookController::class, 'custom'])->name('api.webhooks.custom');
