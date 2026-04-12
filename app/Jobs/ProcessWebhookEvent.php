<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\Webhooks\CustomParser;
use App\Services\Webhooks\AutomationEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWebhookEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WebhookEvent $event) {}

    public function handle(): void
    {
        try {
            // All webhooks are now processed via Custom Dynamic Mapping
            if (!str_starts_with($this->event->source, 'custom_')) {
                $this->event->update(['status' => 'failed', 'error_message' => 'Native sources are deprecated']);
                return;
            }

            $uuid = str_replace('custom_', '', $this->event->source);
            $normalized = CustomParser::parse($this->event->payload, $uuid);

            if (!$normalized) {
                $this->event->update(['status' => 'failed', 'error_message' => 'Mapping failed or webhook not found']);
                return;
            }

            $this->event->update([
                'normalized_data' => $normalized,
                'event_type'      => $normalized['event'],
                'status'          => 'processed',
                'processed_at'    => now(),
            ]);

            // Add auto-password for new users to normalized data so all actions can use it (e.g. {{password}} in emails)
            $email = $normalized['buyer_email'] ?? null;
            if ($email && !\App\Models\User::where('email', $email)->exists()) {
                $tempPassword = str()->random(10);
                $normalized['password'] = $tempPassword;
                $this->event->update(['normalized_data' => $normalized]);
            }

            // Dispatch automation rules
            AutomationEngine::process($this->event->fresh());

        } catch (\Exception $e) {
            Log::error("ProcessWebhookEvent failed for event #{$this->event->id}: " . $e->getMessage());
            $this->event->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
