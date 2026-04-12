<?php

namespace App\Http\Controllers;

use App\Models\WebhookConfig;
use App\Models\WebhookEvent;
use App\Jobs\ProcessWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function hotmart(Request $request)
    {
        $secret = config('services.hotmart.webhook_secret');
        if ($secret) {
            $signature = $request->header('X-Hotmart-Webhook-Token') ?? $request->header('X-Hotmart-Hottok');
            if (!$signature || $signature !== $secret) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $this->storeAndProcess($request, 'hotmart');
    }

    public function cakto(Request $request)
    {
        $secret = config('services.cakto.webhook_secret');
        if ($secret) {
            $token = $request->header('X-Cakto-Token') ?? $request->header('Authorization');
            if (!$token || !str_contains($token, $secret)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $this->storeAndProcess($request, 'cakto');
    }

    public function wikify(Request $request)
    {
        $secret = config('services.wikify.webhook_secret');
        if ($secret) {
            $signature = $request->header('X-Wikify-Signature');
            $computed  = hash_hmac('sha256', $request->getContent(), $secret);
            if (!$signature || !hash_equals($computed, $signature)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $this->storeAndProcess($request, 'wikify');
    }

    public function custom(Request $request, string $uuid)
    {
        $customHook = \App\Models\CustomWebhook::where('uuid', $uuid)->first();
        
        if (!$customHook) {
            return response()->json(['error' => 'Webhook not found'], 404);
        }

        $payload = $request->all();
        // Update the last received payload so the user can map it
        $customHook->update(['last_payload' => $payload]);

        return $this->storeAndProcess($request, 'custom_' . $uuid);
    }

    private function storeAndProcess(Request $request, string $source)
    {
        try {
            $payload = $request->all();
            $eventType = $payload['event'] ?? $payload['type'] ?? 'unknown';

            $event = WebhookEvent::create([
                'source'     => $source,
                'event_type' => $eventType,
                'payload'    => $payload,
                'status'     => 'pending',
            ]);

            ProcessWebhookEvent::dispatch($event);

            return response()->json(['status' => 'received', 'id' => $event->id]);
        } catch (\Exception $e) {
            Log::error("Webhook {$source} error: " . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}
