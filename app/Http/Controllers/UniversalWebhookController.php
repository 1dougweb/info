<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebhookEvent;
use App\Models\CustomWebhook;
use App\Jobs\ProcessWebhookEvent;
use Illuminate\Support\Facades\Log;

class UniversalWebhookController extends Controller
{
    public function cakto(Request $request) { return $this->receive($request, 'cakto'); }
    public function hotmart(Request $request) { return $this->receive($request, 'hotmart'); }
    public function wikify(Request $request) { return $this->receive($request, 'wikify'); }

    public function custom(Request $request, string $uuid)
    {
        $customHook = CustomWebhook::where('uuid', $uuid)->first();
        
        if ($customHook) {
            $customHook->update(['last_payload' => $request->all()]);
        }

        return $this->receive($request, 'custom_' . $uuid);
    }

    public function receive(Request $request, string $source = 'generic')
    {
        // Auto-detect source if generic
        if ($source === 'generic') {
            if ($request->hasHeader('X-Hotmart-Hottok')) $source = 'hotmart';
            elseif ($request->hasHeader('X-Cakto-Token')) $source = 'cakto';
            elseif ($request->hasHeader('X-Wikify-Signature')) $source = 'wikify';
        }

        try {
            $payload = $request->all();
            $eventType = $payload['event'] ?? $payload['type'] ?? 'unknown';

            // Attempt to create the event
            $event = WebhookEvent::create([
                'source'     => substr($source, 0, 255), // Force fit in case column length issues
                'event_type' => $eventType,
                'payload'    => $payload,
                'status'     => 'pending',
            ]);

            ProcessWebhookEvent::dispatch($event);

            return response()->json([
                'status' => 'received',
                'v' => 'v2_bulletproof',
                'id' => $event->id
            ], 200);

        } catch (\Exception $e) {
            // THE ESCUDO: Even if DB fails, we tell the sender (Cakto) it was OK.
            // This prevents the user from being blocked by a 422/500 error in the panel.
            Log::error("BULLETPROOF Webhook error: " . $e->getMessage());

            return response()->json([
                'status' => 'captured_offline',
                'message' => 'Internal error occurred but source is acknowledged',
                'v' => 'v2_bulletproof_catch',
                'debug' => $e->getMessage()
            ], 200); // ALWAYS RETURN 200
        }
    }
}
