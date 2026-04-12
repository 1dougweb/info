<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\Mail\DynamicMailService;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function updateSmtp(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required',
            'smtp_port' => 'required',
            'smtp_user' => 'required',
            'mail_from_address' => 'required|email',
        ]);

        Setting::set('smtp_host', $request->smtp_host);
        Setting::set('smtp_port', $request->smtp_port);
        Setting::set('smtp_encryption', $request->smtp_encryption);
        Setting::set('smtp_user', $request->smtp_user);
        
        if ($request->smtp_pass) {
            Setting::set('smtp_pass', $request->smtp_pass, true);
        }

        Setting::set('mail_from_address', $request->mail_from_address);
        Setting::set('mail_from_name', $request->mail_from_name);

        return back()->with('success', 'Configurações de SMTP atualizadas com sucesso!');
    }

    public function testSmtp(Request $request)
    {
        try {
            DynamicMailService::applySettings();

            Mail::raw('Este é um e-mail de teste para validar as configurações de SMTP da sua Área de Membros.', function ($message) use ($request) {
                $message->to($request->user()->email)
                        ->subject('Teste de Conexão SMTP');
            });

            return response()->json(['success' => true, 'message' => 'E-mail de teste enviado com sucesso para ' . $request->user()->email]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao enviar e-mail: ' . $e->getMessage()]);
        }
    }
}
