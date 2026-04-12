@extends('layouts.admin')
@section('title', 'Membros')
@section('breadcrumb', 'Membros')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-people-fill me-2"></i> Membros</h1>
</div>

{{-- Search --}}
<div class="card mb-6">
    <div class="card-body" style="padding: 16px 24px;">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou e-mail..." value="{{ request('search') }}" style="max-width: 360px;">
            <select name="role" class="form-control" style="width: 160px;">
                <option value="">Todos os papéis</option>
                <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Membros</option>
                <option value="admin"  {{ request('role') === 'admin'  ? 'selected' : '' }}>Admins</option>
            </select>
            <button class="btn btn-primary">Buscar</button>
            @if (request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Limpar</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap" style="border: none; border-radius: 0;">
        <table>
            <thead>
                <tr>
                    <th>Membro</th>
                    <th>Telefone</th>
                    <th>Papel</th>
                    <th>Matrículas</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="sidebar-user-avatar" style="width:32px; height:32px; font-size: 0.75rem;">{{ strtoupper(substr($user->name,0,1)) }}</div>
                            <div>
                                <div class="font-semibold">{{ $user->name }}</div>
                                <div class="text-xs text-muted">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $user->phone ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge-purple' : 'badge-blue' }}">
                            {{ $user->role === 'admin' ? 'Admin' : 'Membro' }}
                        </span>
                    </td>
                    <td>{{ $user->enrollments_count }}</td>
                    <td class="text-muted text-sm">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary btn-sm">Ver</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted p-6">Nenhum membro encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
    <div style="padding: 16px 24px;">{{ $users->links() }}</div>
    @endif
</div>
@endsection
