<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código — MembersArea</title>
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
                <div class="auth-logo-icon"><i class="bi bi-shield-lock-fill"></i></div>
            @endif
            <h1 class="auth-title">Verificar Código</h1>
            <p class="auth-sub">Digite o código de 6 dígitos que enviamos para seu e-mail</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success mb-4">
                <i class="bi bi-info-circle-fill"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.verify.post') }}" class="auth-form">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="form-group">
                <label class="form-label">Código de Verificação</label>
                <input type="text" name="code" class="form-control text-center" placeholder="000000" maxlength="6" style="letter-spacing: 10px; font-size: 1.5rem; font-weight: bold;" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Verificar Código
            </button>
        </form>

        <p class="text-center text-sm text-muted mt-6">
            Não recebeu o e-mail? <a href="{{ route('password.request') }}">Tentar novamente</a>
        </p>
    </div>
</div>
</body>
</html>
