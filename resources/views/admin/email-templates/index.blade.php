@extends('layouts.admin')
@section('title', 'Modelos de E-mail')
@section('breadcrumb', 'Automações › Modelos de E-mail')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-layout-text-window-reverse me-2"></i> Modelos de E-mail</h1>
    <p class="text-muted text-sm mt-2">Defina o conteúdo padrão dos e-mails disparados pelas automações.</p>
</div>

<div class="grid-2" style="gap: 24px;">
    <div class="flex flex-col gap-4">
        @foreach ($templates as $template)
        <div class="card cursor-pointer hover-card {{ request()->query('trigger') == $template->trigger ? 'border-primary' : '' }}" 
             onclick="window.location.href='?trigger={{ $template->trigger }}'">
            <div class="card-body p-4 flex justify-between items-center">
                <div>
                    <h4 class="font-bold">{{ $triggers[$template->trigger] ?? $template->trigger }}</h4>
                    <p class="text-xs text-muted">{{ $template->subject ?? '(Sem assunto definido)' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($template->is_active)
                        <span class="badge badge-green">Ativo</span>
                    @else
                        <span class="badge badge-gray">Inativo</span>
                    @endif
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @php
        $selectedTrigger = request()->query('trigger', $templates->first()->trigger ?? null);
        $selectedTemplate = $templates->where('trigger', $selectedTrigger)->first();
    @endphp

    <div class="card">
        @if ($selectedTemplate)
            <div class="card-header flex justify-between items-center">
                <h3 class="font-semibold">Editando: {{ $triggers[$selectedTrigger] ?? $selectedTrigger }}</h3>
            </div>
            <form method="POST" action="{{ route('admin.email-templates.update', $selectedTemplate) }}">
                @csrf @method('PUT')
                <div class="card-body bg-surface-2">
                    <div class="form-group">
                        <label class="form-label">Assunto do E-mail</label>
                        <input type="text" name="subject" class="form-control" value="{{ $selectedTemplate->subject }}" placeholder="Ex: @{{buyer_name}}, seu boleto chegou!" required>
                    </div>

                    <div class="form-group mt-4">
                        <label class="form-label">Corpo do E-mail (HTML permitido)</label>
                        <textarea name="body" class="form-control" rows="12" required style="font-family: monospace; font-size: 0.9rem;">{{ $selectedTemplate->body }}</textarea>
                    </div>

                    <div class="mt-4 p-3 bg-surface-1 rounded border border-faint">
                        <h4 class="text-xs font-bold mb-2 uppercase text-muted">Variáveis Disponíveis:</h4>
                        <div class="flex flex-wrap gap-2">
                            <code class="text-xs">@{{buyer_name}}</code>
                            <code class="text-xs">@{{buyer_email}}</code>
                            <code class="text-xs">@{{product_name}}</code>
                            <code class="text-xs">@{{amount}}</code>
                            <code class="text-xs">@{{billet_url}}</code>
                            <code class="text-xs">@{{pix_code}}</code>
                            <code class="text-xs">@{{checkout_url}}</code>
                        </div>
                    </div>

                    <label class="form-check mt-6">
                        <input type="checkbox" name="is_active" value="1" {{ $selectedTemplate->is_active ? 'checked' : '' }}> Modelo Ativo
                    </label>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-full">Salvar Alterações</button>
                </div>
            </form>
        @else
            <div class="card-body text-center py-20 text-muted">
                <i class="bi bi-cursor fs-1"></i>
                <p class="mt-4">Selecione um evento à esquerda para editar o modelo.</p>
            </div>
        @endif
    </div>
</div>

<style>
.hover-card:hover { border-color: var(--primary-soft); transform: translateX(5px); transition: all 0.2s; }
.border-primary { border: 2px solid var(--primary) !important; }
</style>
@endsection
