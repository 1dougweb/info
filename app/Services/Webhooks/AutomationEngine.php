<?php

namespace App\Services\Webhooks;

use App\Models\Automation;
use App\Models\WebhookEvent;
use App\Services\Actions\GrantAccessAction;
use App\Services\Actions\RevokeAccessAction;
use App\Services\Actions\SendEmailAction;
use Illuminate\Support\Facades\Log;

class AutomationEngine
{
    public static function process(WebhookEvent $event): void
    {
        $data = $event->normalized_data;
        if (!$data) return;

        $automations = Automation::where('is_active', true)
            ->where('trigger', $data['event'])
            ->where(function ($q) use ($data) {
                $q->where('source', 'any')
                  ->orWhere('source', $data['source']);
            })
            ->where(function ($q) use ($data) {
                $q->whereNull('source_product_id')
                  ->orWhere('source_product_id', $data['product_id']);
            })
            ->get();

        foreach ($automations as $automation) {
            try {
                // Check for custom conditions if any
                if ($automation->conditions) {
                    // Basic implementation of conditions check can be added here
                    // e.g. if conditions['min_amount'] is set and $data['amount'] < min_amount, skip.
                }

                $delayInSeconds = $automation->delay_seconds ?? 0;

                if ($delayInSeconds > 0) {
                    \App\Models\ScheduledTask::create([
                        'automation_id' => $automation->id,
                        'user_email'    => $data['buyer_email'] ?? 'unknown',
                        'payload'       => $data,
                        'execute_at'    => now()->addSeconds($delayInSeconds),
                        'status'        => 'pending',
                    ]);
                    Log::info("Automation #{$automation->id} scheduled for {$data['buyer_email']} at " . now()->addSeconds($delayInSeconds));
                } else {
                    \App\Jobs\ExecuteAutomationAction::dispatch($automation, $data);
                    Log::info("Automation #{$automation->id} dispatched immediately for {$data['buyer_email']}.");
                }

            } catch (\Exception $e) {
                Log::error("Failed to dispatch Automation #{$automation->id}: " . $e->getMessage());
            }
        }
    }
}
