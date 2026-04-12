<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebhookEvent;
use App\Models\CustomWebhook;
use App\Jobs\ProcessWebhookEvent;
use Illuminate\Support\Facades\Log;

class UniversalWebhookController extends Controller
{
    /**
     * Receives webhooks from custom dynamic sources.
     */
    public function custom(Request $request, string $uuid)
    {
        $customHook = CustomWebhook::where('uuid', $uuid)->first();
        
        if ($customHook) {
            $customHook->update(['last_payload' => $request->all()]);
        }

        return $this->receive($request, 'custom_' . $uuid);
    }

    /**
     * Internal receipt logic with bulletproof response.
     */
    public function receive(Request $request, string $source = 'generic')
    {
        try {
            $payload = $request->all();
            
            // For custom hooks, we might already know the event type from the parser,
            // but we store a basic one here and refine it during processing.
            $eventType = $payload['event'] ?? $payload['type'] ?? 'unknown';

            // Create the event record
            $event = WebhookEvent::create([
                'source'     => substr($source, 0, 255),
                'event_type' => $eventType,
                'payload'    => $payload,
                'status'     => 'pending',
            ]);

            // Dispatch asynchonous processing
            ProcessWebhookEvent::dispatch($event);

            return response()->json([
                'status' => 'received',
                'id' => $event->id
            ], 200);

        } catch (\Exception $e) {
            // ALWAYS RETURN 200 to external senders to prevent retries or blocking.
            Log::error("Webhook receipt error: " . $e->getMessage());

            return response()->json([
                'status' => 'captured_offline',
                'message' => 'Internal error occurred but source is acknowledged',
                'debug' => $e->getMessage()
            ], 200);
        }
    }
}
