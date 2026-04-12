<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — MembersArea</title>
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
            <h1 class="auth-title">Criar conta</h1>
            <p class="auth-sub">Junte-se à plataforma MembersArea</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-error mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <form method="POST" action="/register" class="auth-form">
            @csrf
            <div class="form-group">
                <label class="form-label">Nome completo</label>
                <input type="text" name="name" class="form-control" placeholder="Seu nome" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="seu@email.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Telefone <span class="text-faint">(opcional)</span></label>
                <input type="tel" name="phone" class="form-control" placeholder="(11) 99999-9999" value="{{ old('phone') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar senha</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a senha" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Criar minha conta
            </button>
        </form>

        <div class="auth-divider"><span>ou</span></div>

        <p class="text-center text-sm text-muted">
            Já tem conta? <a href="/login">Fazer login</a>
        </p>
    </div>
</div>
</body>
</html>
