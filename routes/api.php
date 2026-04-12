<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UniversalWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/webhooks/test', fn() => response()->json(['status' => 'ok', 'time' => now()->toDateTimeString(), 'v' => 'v2_bulletproof_test']));

Route::post('/webhooks/custom/{uuid}', [UniversalWebhookController::class, 'custom'])->name('api.webhooks.custom');
