<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha — MembersArea</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @include('layouts.partials.branding')
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            @if ($logo = \App\Models\Setting::get('branding_logo'))
                <img src="{{ asset($logo) }}" alt="Logo" style="height: 64px; max-width: 200px; object-fit: contain; margin: 0 auto 24px;">
            @else
                <div class="auth-logo-icon"><i class="bi bi-key-fill"></i></div>
            @endif
            <h1 class="auth-title">Recuperar Senha</h1>
            <p class="auth-sub">Digite seu e-mail para receber o código</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-4">
                <i class="bi bi-check2-circle"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="seu@email.com" value="{{ old('email') }}" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Enviar código de acesso
            </button>
        </form>

        <div class="auth-divider"><span>ou</span></div>

        <p class="text-center text-sm text-muted">
            Lembrou a senha? <a href="/login">Voltar ao login</a>
        </p>
    </div>
</div>
</body>
</html>
