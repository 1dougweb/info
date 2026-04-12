<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MembersArea</title>
    <meta name="description" content="Acesse a plataforma MembersArea">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon"><i class="bi bi-lightning-fill"></i></div>
            <h1 class="auth-title">Bem-vindo de volta</h1>
            <p class="auth-sub">Acesse sua área de membros</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ $errors->first() }}</div>
        </div>
        @endif

        <form method="POST" action="/login" class="auth-form">
            @csrf
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="seu@email.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="flex items-center justify-between">
                <label class="form-check">
                    <input type="checkbox" name="remember"> Lembrar de mim
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Entrar na plataforma
            </button>
        </form>

        <div class="auth-divider"><span>ou</span></div>

        <p class="text-center text-sm text-muted">
            Não tem conta? <a href="/register">Criar conta</a>
        </p>
    </div>
</div>
</body>
</html>
