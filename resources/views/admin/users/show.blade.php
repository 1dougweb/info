@extends('layouts.admin')
@section('title', $user->name)
@section('breadcrumb', 'Membros › ' . $user->name)

@section('content')
<div class="page-header">
    <div class="flex items-center gap-4">
        <div class="sidebar-user-avatar" style="width:56px; height:56px; font-size:1.2rem;">{{ strtoupper(substr($user->name,0,1)) }}</div>
        <div>
            <h1 class="page-title">{{ $user->name }}</h1>
            <p class="text-muted text-sm">{{ $user->email }} · Cadastrado em {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
</div>

@if(session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

<div class="grid-2" style="gap: 24px; align-items: start;">

    {{-- Enrollments --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Matrículas ({{ $enrollments->count() }})</h3>
        </div>
        <div>
            @forelse ($enrollments as $enrollment)
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 24px; border-bottom: 1px solid var(--border-soft);">
                <div>
                    <div class="font-semibold text-sm">{{ $enrollment->product->title }}</div>
                    <div class="text-xs text-muted">{{ $enrollment->source_badge }} · {{ $enrollment->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge {{ $enrollment->status === 'active' ? 'badge-green' : 'badge-red' }}">
                        {{ $enrollment->status === 'active' ? 'Ativo' : 'Cancelado' }}
                    </span>
                    @if ($enrollment->status === 'active')
                    <form method="POST" action="{{ route('admin.users.revoke', $user) }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $enrollment->product_id }}">
                        <button class="btn btn-danger btn-sm">Revogar</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.users.grant', $user) }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $enrollment->product_id }}">
                        <button class="btn btn-primary btn-sm">Ativar</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-muted">Sem matrículas</div>
            @endforelse
        </div>
    </div>

    {{-- Actions --}}
    <div style="display: flex; flex-direction: column; gap: 16px;">

        {{-- Grant access --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Liberar acesso</h3></div>
            <form method="POST" action="{{ route('admin.users.grant', $user) }}">
                @csrf
                <div class="card-body" style="display: flex; flex-direction: column; gap: 12px;">
                    <div class="form-group">
                        <select name="product_id" class="form-control" required>
                            <option value="">Selecionar produto...</option>
                            @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary btn-block">Liberar Acesso Manual</button>
                </div>
            </form>
        </div>

        {{-- Role --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Papel do usuário</h3></div>
            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                @csrf @method('PUT')
                <div class="card-body" style="display: flex; gap: 12px;">
                    <select name="role" class="form-control">
                        <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Membro</option>
                        <option value="admin"  {{ $user->role === 'admin'  ? 'selected' : '' }}>Admin</option>
                    </select>
                    <button class="btn btn-secondary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
