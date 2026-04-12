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
        Log::info("ProcessWebhookEvent — starting for event #{$this->event->id} | source: {$this->event->source}");

        try {
            if (!str_starts_with($this->event->source, 'custom_')) {
                $this->event->update([
                    'status'        => 'failed',
                    'error_message' => 'Native sources are deprecated. Use a Custom Webhook.',
                ]);
                Log::warning("ProcessWebhookEvent — Event #{$this->event->id} rejected: non-custom source.");
                return;
            }

            $uuid       = str_replace('custom_', '', $this->event->source);
            $normalized = CustomParser::parse($this->event->payload, $uuid);

            if (empty($normalized) || empty($normalized['buyer_email'])) {
                $this->event->update([
                    'status'        => 'failed',
                    'error_message' => 'Mapping failed: no buyer_email resolved. Check webhook mapping configuration.',
                ]);
                Log::warning("ProcessWebhookEvent — Event #{$this->event->id} failed: mapping produced no buyer_email.");
                return;
            }

            // Pre-generate a temporary password for new users so {{ password }} is available in emails
            $email = $normalized['buyer_email'];
            if (!\App\Models\User::where('email', $email)->exists()) {
                $normalized['password'] = str()->random(10);
            }

            $this->event->update([
                'normalized_data' => $normalized,
                'event_type'      => $normalized['event'],
                'status'          => 'processed',
                'processed_at'    => now(),
            ]);

            Log::info("ProcessWebhookEvent — Event #{$this->event->id} normalized.", [
                'event_type'  => $normalized['event'],
                'buyer_email' => $normalized['buyer_email'],
            ]);

            // Hand off to automation engine — each automation handles its own try/catch
            AutomationEngine::process($this->event->fresh());

        } catch (\Throwable $e) {
            Log::error("ProcessWebhookEvent — Event #{$this->event->id} threw an unexpected exception: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $this->event->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
