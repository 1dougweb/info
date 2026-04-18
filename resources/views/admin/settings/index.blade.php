@extends('layouts.admin')
@section('title', 'Configurações de SMTP')
@section('breadcrumb', 'Configurações › SMTP')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-envelope-at me-2"></i> Configurações de SMTP</h1>
    <p class="text-muted text-sm mt-2">Configure os dados do seu servidor de e-mail para que a plataforma possa enviar notificações automáticas.</p>
</div>

@if (session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

<div class="grid-2" style="gap: 24px; align-items: start;">
    <div class="card">
        <div class="card-header"><h3 class="font-semibold">Servidor de Saída</h3></div>
        <form method="POST" action="{{ route('admin.settings.smtp.update') }}">
            @csrf
            <div class="card-body">
                <div class="grid-2" style="gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Host SMTP *</label>
                        <input type="text" name="smtp_host" class="form-control" value="{{ \App\Models\Setting::get('smtp_host') }}" placeholder="smtp.exemplo.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Porta *</label>
                        <input type="number" name="smtp_port" class="form-control" value="{{ \App\Models\Setting::get('smtp_port', 587) }}" placeholder="587" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Criptografia</label>
                        <select name="smtp_encryption" class="form-control">
                            <option value="tls" {{ \App\Models\Setting::get('smtp_encryption') == 'tls' ? 'selected' : '' }}>TLS (Recomendado)</option>
                            <option value="ssl" {{ \App\Models\Setting::get('smtp_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ \App\Models\Setting::get('smtp_encryption') == 'none' ? 'selected' : '' }}>Nenhuma</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Usuário *</label>
                        <input type="text" name="smtp_user" class="form-control" value="{{ \App\Models\Setting::get('smtp_user') }}" placeholder="contato@seudominio.com" required>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label class="form-label">Senha SMTP (Opcional se já salvo)</label>
                    <input type="password" name="smtp_pass" class="form-control" placeholder="••••••••••••">
                    <span class="form-hint">Senha criptografada no banco de dados.</span>
                </div>

                <hr class="my-6 border-faint">

                <div class="grid-2" style="gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">E-mail de Remetente *</label>
                        <input type="email" name="mail_from_address" class="form-control" value="{{ \App\Models\Setting::get('mail_from_address') }}" placeholder="nao-responda@seudominio.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nome de Remetente</label>
                        <input type="text" name="mail_from_name" class="form-control" value="{{ \App\Models\Setting::get('mail_from_name') }}" placeholder="Área de Membros">
                    </div>
                </div>
            </div>
            <div class="card-footer flex justify-between p-6" style="border-top: 1px solid var(--border-soft);">
                <button type="button" id="btnTestSmtp" class="btn btn-secondary">
                    <span class="spinner-border spinner-border-sm d-none me-2"></span>
                    <i class="bi bi-send-check"></i> Testar Conexão
                </button>
                <button type="submit" class="btn btn-primary">Salvar Configurações</button>
            </div>
        </form>
    </div>

    <div class="card" style="border: 1px solid var(--primary-soft); background: var(--surface-2);">
        <div class="card-body">
            <h3 class="font-bold mb-3" style="color: var(--primary);"><i class="bi bi-info-circle"></i> Instruções SMTP</h3>
            <ul class="text-sm text-muted" style="padding-left: 20px; line-height: 1.8;">
                <li><strong>Gmail:</strong> Use o host <code>smtp.gmail.com</code> e porta <code>587</code>. Você precisa gerar uma "Senha de App" nas configurações da sua conta Google.</li>
                <li><strong>SendGrid/Mailtrap:</strong> Ótimas opções para garantir que seus e-mails não caiam no Spam.</li>
                <li><strong>E-mail próprio:</strong> Certifique-se que seu servidor permite conexões externas via SMTP.</li>
            </ul>
            <div class="alert alert-info mt-6 text-xs">
                <i class="bi bi-lightbulb me-1"></i> As configurações salvas nesta tela substituirão os dados padrão do sistema.
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('btnTestSmtp').addEventListener('click', async function() {
    const btn = this;
    const spinner = btn.querySelector('.spinner-border');
    
    if (!confirm('Deseja enviar um e-mail de teste para o seu e-mail de administrador agora?')) return;

    btn.disabled = true;
    spinner.classList.remove('d-none');

    try {
        const response = await fetch('{{ route("admin.settings.smtp.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Sucesso! ' + data.message);
        } else {
            alert('Falha na conexão: ' + data.message);
        }
    } catch (e) {
        alert('Ocorreu um erro ao tentar testar: ' + e.message);
    } finally {
        btn.disabled = false;
        spinner.classList.add('d-none');
    }
});
</script>
@endsection
