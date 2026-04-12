<?php

namespace App\Http\Controllers;

use App\Models\CustomWebhook;
use App\Models\WebhookEvent;
use App\Jobs\ProcessWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CustomWebhookController extends Controller
{
    /**
     * API: Receives the webhook payload from external platforms.
     */
    public function receivePayload(Request $request, string $uuid)
    {
        try {
            $customHook = CustomWebhook::where('uuid', $uuid)->first();
            
            if ($customHook) {
                $customHook->update(['last_payload' => $request->all()]);
            }

            $payload = $request->all();
            $eventType = $payload['event'] ?? $payload['type'] ?? 'unknown';

            $event = WebhookEvent::create([
                'source'     => 'custom_' . $uuid,
                'event_type' => $eventType,
                'payload'    => $payload,
                'status'     => 'pending',
            ]);

            // Dispatch synchronous processing to bypass Hostinger's queue cache permanently
            ProcessWebhookEvent::dispatchSync($event);

            return response()->json([
                'status' => 'received',
                'id' => $event->id
            ], 200);

        } catch (\Exception $e) {
            Log::error("Webhook receipt error: " . $e->getMessage());

            return response()->json([
                'status' => 'captured_offline',
                'message' => 'Internal error occurred but source is acknowledged',
                'debug' => $e->getMessage()
            ], 200);
        }
    }
    public function index()
    {
        $webhooks = CustomWebhook::latest()->get();
        return view('admin.webhooks.index', compact('webhooks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        CustomWebhook::create(['name' => $request->name]);
        return back()->with('success', 'Webhook criado!');
    }

    public function show(CustomWebhook $webhook)
    {
        // Renamed parameter to $webhook to match the resource 'webhooks' binding
        return view('admin.webhooks.show', ['customWebhook' => $webhook]);
    }

    public function update(Request $request, CustomWebhook $webhook)
    {
        $request->validate(['mapping' => 'nullable|array']);
        
        $webhook->update([
            'mapping' => $request->mapping
        ]);

        return back()->with('success', 'Mapeamento salvo com sucesso!');
    }

    public function destroy(CustomWebhook $webhook)
    {
        $webhook->delete();
        return redirect()->route('admin.webhooks.index')->with('success', 'Webhook removido.');
    }
}
