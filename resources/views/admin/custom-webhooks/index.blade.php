@extends('layouts.admin')
@section('title', 'Webhooks Customizados')
@section('breadcrumb', 'Integrações › Webhooks Customizados')

@section('content')
<div class="page-header flex justify-between items-center">
    <div>
        <h1 class="page-title"><i class="bi bi-diagram-3 me-2"></i> Webhooks Customizados (Catch)</h1>
        <p class="text-muted text-sm mt-2">Crie URLs únicas para receber dados de qualquer plataforma externa.</p>
    </div>
    <div class="flex gap-3">
        <button x-data @click="$dispatch('open-create-modal')" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo Webhook</button>
    </div>
</div>


@if (session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

<div class="grid-3" style="gap: 16px;">
    @forelse ($webhooks as $webhook)
    <div class="card">
        <div class="card-body">
            <h3 class="font-semibold text-lg mb-1">{{ $webhook->name }}</h3>
            <p class="text-xs text-muted mb-4">{{ $webhook->uuid }}</p>
            <div class="flex justify-between items-center mt-4">
                <a href="{{ route('admin.custom-webhooks.show', $webhook) }}" class="btn btn-secondary btn-sm"><i class="bi bi-gear"></i> Mapear Variáveis</a>
                <form method="POST" action="{{ route('admin.custom-webhooks.destroy', $webhook) }}" onsubmit="return confirm('Remover?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-ghost btn-sm text-red-500"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column: 1/-1; padding: 40px; text-align: center;">
        <div class="text-muted mb-4"><i class="bi bi-inboxes" style="font-size: 3rem;"></i></div>
        <p>Nenhum webhook customizado criado. Crie um para começar a receber dados de onde quiser!</p>
    </div>
    @endforelse
</div>

<div x-data="{ open: false }" @open-create-modal.window="open = true">
    <div class="modal-overlay" x-show="open" x-cloak @click.self="open=false">
        <div class="modal">
            <div class="modal-header">
                <h3 class="card-title">Novo Webhook Customizado</h3>
                <button @click="open=false" class="btn btn-ghost btn-sm"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.custom-webhooks.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label class="form-label">Nome da Plataforma (Ex: PerfectPay, Braip, etc) *</label>
                        <input type="text" name="name" class="form-control" autocomplete="off" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="open=false" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Gerar URL</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
