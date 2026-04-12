<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/webhooks/test', fn() => response()->json(['status' => 'ok', 'time' => now()->toDateTimeString(), 'v' => 'v2_bulletproof_test']));

// Using Route::any to ensure we catch the webhook even if the platform sends an unusual verb
Route::any('/webhooks/custom/{uuid}', [CustomWebhookController::class, 'receivePayload'])->name('api.webhooks.custom');