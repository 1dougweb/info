<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\Webhooks\HotmartParser;
use App\Services\Webhooks\CaktoParser;
use App\Services\Webhooks\WikifyParser;
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
            if (str_starts_with($this->event->source, 'custom_')) {
                $uuid = str_replace('custom_', '', $this->event->source);
                $normalized = \App\Services\Webhooks\CustomParser::parse($this->event->payload, $uuid);
            } else {
                $normalized = match($this->event->source) {
                    'hotmart' => HotmartParser::parse($this->event->payload),
                    'cakto'   => CaktoParser::parse($this->event->payload),
                    'wikify'  => WikifyParser::parse($this->event->payload),
                    default   => null,
                };
            }

            if (!$normalized) {
                $this->event->update(['status' => 'failed', 'error_message' => 'Unknown source']);
                return;
            }

            $this->event->update([
                'normalized_data' => $normalized,
                'event_type'      => $normalized['event'],
                'status'          => 'processed',
                'processed_at'    => now(),
            ]);

            AutomationEngine::process($this->event->fresh());
        } catch (\Exception $e) {
            Log::error("ProcessWebhookEvent failed for event #{$this->event->id}: " . $e->getMessage());
            $this->event->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
