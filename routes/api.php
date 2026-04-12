<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/hotmart', [WebhookController::class, 'hotmart'])->name('webhooks.hotmart');
Route::post('/webhooks/cakto', [WebhookController::class, 'cakto'])->name('webhooks.cakto');
Route::post('/webhooks/wikify', [WebhookController::class, 'wikify'])->name('webhooks.wikify');

// Universal Smart Webhook
Route::post('/webhooks/v1/receive', [WebhookController::class, 'receive'])->name('api.webhooks.receive');
Route::get('/webhooks/test', fn() => response()->json(['status' => 'ok', 'time' => now()->toDateTimeString(), 'v' => 'debug_v2']));

Route::post('/webhooks/custom/{uuid}', [WebhookController::class, 'custom'])->name('api.webhooks.custom');
