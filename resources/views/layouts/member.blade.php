<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Minha Área') — MembersArea</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body>

<div class="app-layout" x-data>

    {{-- Sidebar --}}
    <aside class="sidebar" :class="$store.sidebar.open ? 'open' : ''">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon"><i class="bi bi-lightning-fill"></i></div>
            <div>
                <div class="sidebar-brand-name">MembersArea</div>
                <div class="sidebar-brand-sub">Área de Membros</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Minha área</div>
            <a href="{{ route('member.dashboard') }}" class="sidebar-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-house-door-fill"></i></span> Início
            </a>
            <a href="{{ route('member.products.index') }}" class="sidebar-link {{ request()->routeIs('member.products*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-mortarboard-fill"></i></span> Catálogo
            </a>
            <a href="{{ route('member.profile') }}" class="sidebar-link {{ request()->routeIs('member.profile') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-person-fill"></i></span> Meu Perfil
            </a>

            @if (auth()->user()->isAdmin())
            <div class="sidebar-section-label">Admin</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class="bi bi-gear"></i></span> Painel Admin
            </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="flex-1 truncate">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Membro</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" title="Sair"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <main class="main-content">
        <div class="topbar">
            <div class="flex items-center gap-3">
                <button class="btn btn-ghost btn-sm" @click="$store.sidebar.toggle()"><i class="bi bi-list fs-5"></i></button>
                <span class="topbar-title">@yield('breadcrumb', 'Início')</span>
            </div>
        </div>

        {{-- Toast --}}
        <div class="toast-container">
            <template x-for="toast in $store.toast.items" :key="toast.id">
                <div class="toast" :class="'alert-' + toast.type">
                    <span x-text="toast.message"></span>
                    <button @click="$store.toast.remove(toast.id)" class="btn btn-ghost btn-sm"><i class="bi bi-x-lg"></i></button>
                </div>
            </template>
        </div>

        @if (session('success'))
        <div x-data x-init="$store.toast.add('{{ session('success') }}', 'success')"></div>
        @endif
        @if (session('error'))
        <div x-data x-init="$store.toast.add('{{ session('error') }}', 'error')"></div>
        @endif

        <div class="page-container fade-in">
            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
