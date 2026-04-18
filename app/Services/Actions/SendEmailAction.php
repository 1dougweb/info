<?php

namespace App\Services\Actions;

use App\Models\Automation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\Mail\DynamicMailService;

class SendEmailAction
{
    public static function execute(Automation $automation, array $data): void
    {
        $config = $automation->action_config ?? [];
        $subject = $config['subject'] ?? '';
        $body = $config['body'] ?? '';
        
        $templateId = $config['template_id'] ?? null;
        $template = null;

        if ($templateId) {
            $template = \App\Models\EmailTemplate::find($templateId);
        }

        // Fallback to searching by trigger if no template ID is provided or found
        if (!$template) {
            $template = \App\Models\EmailTemplate::where('trigger', $automation->trigger)->where('is_active', true)->first();
        }

        if ($template) {
            $subject = empty($subject) ? $template->subject : $subject;
            $body = empty($body) ? $template->body : $body;
        }

        if (empty($subject) || empty($body)) {
            Log::warning("SendEmailAction: Skipping automation #{$automation->id} - No content or active template found.");
            return;
        }

        // Replace tags
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $tag = '{{' . $key . '}}';
                $subject = str_replace($tag, (string) $value, $subject);
                $body = str_replace($tag, (string) $value, $body);
                
                // Suporte para tags no estilo @{{tag}} como mencionado no plano
                $tagArroba = '@{{' . $key . '}}';
                $subject = str_replace($tagArroba, (string) $value, $subject);
                $body = str_replace($tagArroba, (string) $value, $body);
            }
        }

        try {
            // Apply SMTP settings from database
            DynamicMailService::applySettings();

            $to = $data['buyer_email'] ?? null;
            if (!$to) {
                Log::warning("SendEmailAction: No recipient email found for automation #{$automation->id}");
                return;
            }

            $htmlContent = view('layouts.emails.branded', ['content' => $body])->render();

            Mail::html($htmlContent, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            Log::info("Automation #{$automation->id}: Email sent to {$to}");
        } catch (\Exception $e) {
            Log::error("SendEmailAction failed for automation #{$automation->id}: " . $e->getMessage());
        }
    }
}
