<?php

namespace App\Services\Actions;

use App\Models\Automation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendEmailAction
{
    public static function execute(Automation $automation, array $data): void
    {
        $config = $automation->action_config ?? [];
        $subject = $config['subject'] ?? 'Notificação da Área de Membros';
        $body = $config['body'] ?? '';

        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $subject = str_replace('{{' . $key . '}}', (string) $value, $subject);
                $body = str_replace('{{' . $key . '}}', (string) $value, $body);
            }
        }

        // Email sending is logged — integrate with Mail facade or external provider
        Log::info('SendEmailAction triggered', [
            'automation' => $automation->id,
            'to'         => $data['buyer_email'] ?? 'unknown',
            'subject'    => $subject,
            'body'       => $body,
        ]);
        
        // TODO: Implement actual email sending via Laravel Mail
    }
}
