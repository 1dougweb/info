<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniversalWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/webhooks/hotmart', [UniversalWebhookController::class, 'hotmart'])->name('webhooks.hotmart');
Route::post('/webhooks/cakto', [UniversalWebhookController::class, 'cakto'])->name('webhooks.cakto');
Route::post('/webhooks/wikify', [UniversalWebhookController::class, 'wikify'])->name('webhooks.wikify');

// Universal Smart Webhook
Route::post('/webhooks/v1/receive', [UniversalWebhookController::class, 'receive'])->name('api.webhooks.receive');
Route::get('/webhooks/test', fn() => response()->json(['status' => 'ok', 'time' => now()->toDateTimeString(), 'v' => 'v2_bulletproof_test']));

Route::post('/webhooks/custom/{uuid}', [UniversalWebhookController::class, 'custom'])->name('api.webhooks.custom');
