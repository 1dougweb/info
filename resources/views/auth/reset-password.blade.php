<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha — MembersArea</title>
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
                <div class="auth-logo-icon"><i class="bi bi-lock-fill"></i></div>
            @endif
            <h1 class="auth-title">Nova Senha</h1>
            <p class="auth-sub">Crie uma nova senha para sua conta</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Nova Senha</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar Senha</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Alterar Senha
            </button>
        </form>
    </div>
</div>
</body>
</html>
