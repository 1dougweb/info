@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bar-chart-fill me-2"></i> Dashboard</h1>
    <span class="text-muted text-sm">{{ now()->format('d/m/Y') }}</span>
</div>

{{-- Stats --}}
<div class="grid-4 mb-8">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_products'] }}</div>
            <div class="stat-label">Produtos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_members'] }}</div>
            <div class="stat-label">Membros</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_enrollments'] }}</div>
            <div class="stat-label">Matrículas ativas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-link-45deg"></i></div>
        <div>
            <div class="stat-value">{{ $stats['webhook_events'] }}</div>
            <div class="stat-label">Eventos webhook</div>
        </div>
    </div>
</div>

<div class="grid-2 gap-6">
    {{-- Recent enrollments --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Matrículas recentes</h3>
        </div>
        <div class="table-wrap" style="border: none; border-radius: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Membro</th>
                        <th>Produto</th>
                        <th>Fonte</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentEnrollments as $enrollment)
                    <tr>
                        <td>
                            <div class="font-semibold">{{ $enrollment->user->name }}</div>
                            <div class="text-xs text-muted">{{ $enrollment->user->email }}</div>
                        </td>
                        <td class="truncate" style="max-width: 150px;">{{ $enrollment->product->title }}</td>
                        <td>
                            <span class="badge badge-blue">{{ $enrollment->source_badge }}</span>
                        </td>
                        <td>
                            @if ($enrollment->status === 'active')
                                <span class="badge badge-green">Ativo</span>
                            @elseif ($enrollment->status === 'cancelled')
                                <span class="badge badge-red">Cancelado</span>
                            @else
                                <span class="badge badge-yellow">{{ $enrollment->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">Nenhuma matrícula ainda</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent webhook events --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Eventos webhook recentes</h3>
            <a href="{{ route('admin.webhooks.index') }}" class="btn btn-ghost btn-sm">Ver todos</a>
        </div>
        <div class="card-body" style="padding: 0;">
            @forelse ($recentEvents as $event)
            <div style="padding: 12px 24px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 12px;">
                <div class="event-status-dot {{ 'dot-' . $event->status }}"></div>
                <div class="flex-1">
                    <div class="text-sm font-semibold">{{ $event->event_type ?? 'unknown' }}</div>
                    <div class="text-xs text-muted">{{ ucfirst($event->source) }} · {{ $event->created_at->diffForHumans() }}</div>
                </div>
                <span class="badge {{ $event->status === 'processed' ? 'badge-green' : ($event->status === 'failed' ? 'badge-red' : 'badge-yellow') }}">
                    {{ $event->status }}
                </span>
            </div>
            @empty
            <div class="p-6 text-center text-muted">Nenhum evento recebido</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
