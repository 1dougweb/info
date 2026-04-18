<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\ResetCodeMail;
use App\Services\Mail\DynamicMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $email = $request->email;
        $code = rand(100000, 999999);

        // Save code to DB
        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $email],
            ['code' => $code, 'created_at' => now()]
        );

        // Send Email
        try {
            DynamicMailService::applySettings();
            Mail::to($email)->send(new ResetCodeMail($code));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Erro ao enviar e-mail. Verifique suas configurações de SMTP.']);
        }

        return redirect()->route('password.verify.form', ['email' => $email])
            ->with('status', 'Código enviado para seu e-mail.');
    }

    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-code', ['email' => $request->email]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$record || now()->diffInMinutes($record->created_at) > 15) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        // Store in session that code is verified
        session(['password_reset_email' => $request->email, 'password_reset_verified' => true]);

        return redirect()->route('password.reset.form');
    }

    public function showResetForm()
    {
        if (!session('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function reset(Request $request)
    {
        if (!session('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $email = session('password_reset_email');
        $user = User::where('email', $email)->firstOrFail();

        $user->update(['password' => $request->password]);

        // Clean up
        DB::table('password_reset_codes')->where('email', $email)->delete();
        session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('login')->with('success', 'Senha alterada com sucesso!');
    }
}
