@extends('layouts.member')
@section('title', 'Meu Perfil')
@section('breadcrumb', 'Meu Perfil')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-person-fill me-2"></i> Meu Perfil</h1>
</div>

<div class="card" style="max-width: 560px;">
    <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body" style="display: flex; flex-direction: column; gap: 20px;">

            @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check2"></i> {{ session('success') }}</div>
            @endif
            @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif

            <div class="flex items-center gap-4 mb-4">
                <div class="sidebar-user-avatar" style="width:64px; height:64px; font-size:1.5rem; overflow:hidden;">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <div class="font-semibold">{{ $user->name }}</div>
                    <div class="text-muted text-sm">{{ $user->email }}</div>
                    <div class="mt-2">
                        <label class="btn btn-secondary btn-sm" style="cursor: pointer;">
                            <i class="bi bi-camera-fill me-1"></i> Alterar Foto
                            <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.parentElement.querySelector('span').innerText = 'Foto selecionada'">
                            <span class="ms-1" style="font-size: 0.75rem; font-weight: normal;"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nome completo</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">E-mail <span class="text-faint">(não editável)</span></label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled style="opacity: 0.5;">
            </div>

            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="(11) 99999-9999">
            </div>

            <hr style="border-color: var(--border-soft);">
            <h4 style="color: var(--text-2); font-size: 0.9rem;">Alterar senha <span class="text-faint">(opcional)</span></h4>

            <div class="form-group">
                <label class="form-label">Nova senha</label>
                <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres">
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar nova senha</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a senha">
            </div>

            <div class="flex justify-end">
                <button class="btn btn-primary">Salvar Perfil</button>
            </div>
        </div>
    </form>
</div>
@endsection
