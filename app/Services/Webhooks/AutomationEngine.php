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
                match($automation->action) {
                    'grant_access'  => GrantAccessAction::execute($automation, $data),
                    'revoke_access' => RevokeAccessAction::execute($automation, $data),
                    'send_email'    => SendEmailAction::execute($automation, $data),
                    default         => null,
                };
            } catch (\Exception $e) {
                Log::error("Automation #{$automation->id} failed: " . $e->getMessage());
            }
        }
    }
}
