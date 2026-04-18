<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;
    public $subjectLine;
    public $htmlBody;

    public function __construct($user, $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;

        $template = EmailTemplate::where('trigger', 'admin_password_reset')->where('is_active', true)->first();
        
        $brandName = config('app.name', 'MembersArea');
        $loginUrl = route('login');

        if ($template) {
            $this->subjectLine = str_replace(
                ['[nome_do_cliente]'], 
                [$user->name], 
                $template->subject
            );
            $this->htmlBody = str_replace(
                ['[nome_do_cliente]', '[nova_senha]', '[link_login]'],
                [$user->name, "<strong>{$plainPassword}</strong>", "<a href=\"{$loginUrl}\">{$loginUrl}</a>"],
                $template->body
            );
        } else {
            $this->subjectLine = "Sua nova senha de acesso - {$brandName}";
            $this->htmlBody = "
                <h2>Olá, {$user->name}!</h2>
                <p>Uma nova senha de acesso foi gerada para você pelo administrador da plataforma.</p>
                <p>Sua nova senha é: <strong>{$plainPassword}</strong></p>
                <p>Acesse a plataforma clicando aqui: <a href=\"{$loginUrl}\">{$loginUrl}</a></p>
            ";
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-password-reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
