@extends('layouts.admin')
@section('title', 'Webhooks')
@section('breadcrumb', 'Integrações › Webhooks')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-link-45deg me-2"></i> Webhooks & Integrações</h1>
</div>

@if(session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

{{-- Configs por plataforma --}}
<div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px;">

    @foreach ($configs as $config)
    @php
        $icons = ['hotmart' => '<i class="bi bi-fire"></i>', 'cakto' => '<i class="bi bi-lightning-fill"></i>', 'wikify' => '<i class="bi bi-globe"></i>'];
        $url = url('/api/webhooks/' . $config->source);
    @endphp

    <div class="webhook-source-card" x-data="{ copied: false, editing: false }">
        <div class="flex items-center gap-4" style="min-width: 180px;">
            <div class="stat-icon">{!! $icons[$config->source] ?? '<i class="bi bi-link-45deg"></i>' !!}</div>
            <div>
                <div class="font-semibold" style="font-size: 1rem; text-transform: capitalize;">{{ $config->source }}</div>
                <span class="badge {{ $config->is_active ? 'badge-green' : 'badge-gray' }}">
                    {{ $config->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
        </div>

        <div class="flex-1">
            <div class="form-label mb-2">URL do Webhook para configurar no checkout:</div>
            <div class="webhook-url-box">
                <span>{{ $url }}</span>
                <button @click="navigator.clipboard.writeText('{{ $url }}'); copied=true; setTimeout(()=>copied=false,2000)"
                        class="btn btn-ghost btn-sm" style="flex-shrink:0;">
                    <span x-show="!copied"><i class="bi bi-clipboard"></i></span>
                    <span x-show="copied" x-cloak style="color: var(--primary);"><i class="bi bi-check2"></i></span>
                </button>
            </div>
        </div>

        <div class="flex gap-3">
            <button @click="editing = !editing" class="btn btn-secondary btn-sm"><i class="bi bi-gear"></i> Config</button>
        </div>

        {{-- Config inline --}}
        <div x-show="editing" x-cloak style="width: 100%; border-top: 1px solid var(--border-soft); padding-top: 16px; margin-top: 8px;">
            <form method="POST" action="{{ route('admin.webhooks.update', $config->source) }}" class="flex gap-3 flex-wrap">
                @csrf @method('PUT')
                <div class="form-group flex-1" style="min-width: 200px;">
                    <label class="form-label">Secret / Token de validação</label>
                    <input type="text" name="secret" class="form-control" value="{{ $config->secret }}" placeholder="Token de validação do webhook">
                </div>
                <div class="form-check" style="align-self: flex-end; margin-bottom: 4px;">
                    <input type="checkbox" name="is_active" value="1" {{ $config->is_active ? 'checked' : '' }}>
                    <span class="form-label">Ativo</span>
                </div>
                <button class="btn btn-primary" style="align-self: flex-end;">Salvar</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- Event Log --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Log de Eventos</h3>
        <span class="badge badge-gray">Últimos 50</span>
    </div>
    <div class="table-wrap" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Fonte</th>
                    <th>Evento</th>
                    <th>Dados normalizados</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                <tr x-data="{ expanded: false }">
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="event-status-dot dot-{{ $event->status }}"></div>
                            <span class="badge {{ $event->status === 'processed' ? 'badge-green' : ($event->status === 'failed' ? 'badge-red' : 'badge-yellow') }}">
                                {{ $event->status }}
                            </span>
                        </div>
                    </td>
                    <td><span class="badge badge-blue" style="text-transform: capitalize;">{{ $event->source }}</span></td>
                    <td class="font-semibold text-sm">{{ $event->event_type ?? 'unknown' }}</td>
                    <td>
                        @if ($event->normalized_data)
                        <button @click="expanded = !expanded" class="btn btn-ghost btn-sm">
                            <span x-show="!expanded">Ver variáveis</span>
                            <span x-show="expanded" x-cloak>Fechar</span>
                        </button>
                        <div x-show="expanded" x-cloak style="margin-top: 8px; background: var(--surface-3); border-radius: 6px; padding: 12px; font-family: monospace; font-size: 0.75rem; color: var(--primary);">
                            @foreach ($event->normalized_data as $key => $val)
                            <div><span style="color: var(--text-3);">{{ '{'.'{' }}{{ $key }}{{ '}'.'}' }}</span> = {{ is_array($val) ? json_encode($val) : $val }}</div>
                            @endforeach
                        </div>
                        @else
                        <span class="text-muted text-sm">—</span>
                        @endif
                    </td>
                    <td class="text-muted text-sm">{{ $event->created_at->format('d/m H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted p-6">Nenhum evento registrado ainda</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
