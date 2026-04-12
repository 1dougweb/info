<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/webhooks/test', fn() => response()->json(['status' => 'ok', 'time' => now()->toDateTimeString(), 'v' => 'v2_bulletproof_test']));

// Legacy route to catch webhooks still pointed to the old /api/ URL in Cakto products
Route::any('/webhooks/custom/{uuid}', [\App\Http\Controllers\CustomWebhookController::class, 'receivePayload']);