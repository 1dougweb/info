<?php

namespace App\Services\Mail;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class DynamicMailService
{
    public static function applySettings(): void
    {
        $smtpHost = Setting::get('smtp_host');
        if (!$smtpHost) return;

        Config::set('mail.mailers.smtp.host', $smtpHost);
        Config::set('mail.mailers.smtp.port', Setting::get('smtp_port', 587));
        Config::set('mail.mailers.smtp.encryption', Setting::get('smtp_encryption', 'tls'));
        Config::set('mail.mailers.smtp.username', Setting::get('smtp_user'));
        Config::set('mail.mailers.smtp.password', Setting::get('smtp_pass'));
        
        $fromEmail = Setting::get('mail_from_address', config('mail.from.address'));
        $fromName = Setting::get('mail_from_name', config('mail.from.name'));

        Config::set('mail.from.address', $fromEmail);
        Config::set('mail.from.name', $fromName);
    }
}
