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

    public function branding()
    {
        return view('admin.settings.branding');
    }

    public function updateBranding(Request $request)
    {
        $request->validate([
            'branding_logo' => 'nullable|image|max:1024',
            'branding_favicon' => 'nullable|image|max:512',
            'branding_preset' => 'required|in:default,green,blue,red,purple,custom',
            'branding_custom_color' => 'nullable|string|size:7',
            'branding_bg_color' => 'nullable|string|size:7',
            'branding_btn_text_color' => 'nullable|string|size:7',
            'branding_badge_color' => 'nullable|string|size:7',
        ]);

        if ($request->hasFile('branding_logo')) {
            $file = $request->file('branding_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/branding'), $filename);
            Setting::set('branding_logo', 'uploads/branding/' . $filename);
        }

        if ($request->hasFile('branding_favicon')) {
            $file = $request->file('branding_favicon');
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/branding'), $filename);
            Setting::set('branding_favicon', 'uploads/branding/' . $filename);
        }

        if ($request->branding_preset === 'custom' && $request->branding_custom_color) {
            Setting::set('branding_custom_color', $request->branding_custom_color);
        }

        // Granular Colors
        Setting::set('branding_bg_color', $request->branding_bg_color);
        Setting::set('branding_btn_text_color', $request->branding_btn_text_color);
        Setting::set('branding_badge_color', $request->branding_badge_color);

        Setting::set('branding_preset', $request->branding_preset);

        return back()->with('success', 'Identidade visual atualizada!');
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
