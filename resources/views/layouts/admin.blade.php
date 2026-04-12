<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MembersArea') — Área de Membros</title>
    <meta name="description" content="Plataforma de cursos e infoprodutos">
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
                <div class="sidebar-brand-sub">Painel Admin</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Principal</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-bar-chart-fill"></i></span> Dashboard
            </a>

            <div class="sidebar-section-label">Conteúdo</div>
            <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-box-seam"></i></span> Produtos
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-people-fill"></i></span> Membros
            </a>

            <div class="sidebar-section-label">Integrações</div>
            <a href="{{ route('admin.webhooks.index') }}" class="sidebar-link {{ request()->routeIs('admin.webhooks*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-link-45deg"></i></span> Webhooks (Nativos)
            </a>
            <a href="{{ route('admin.custom-webhooks.index') }}" class="sidebar-link {{ request()->routeIs('admin.custom-webhooks*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-diagram-3"></i></span> Webhooks Custom
            </a>
            <a href="{{ route('admin.automations.index') }}" class="sidebar-link {{ request()->routeIs('admin.automations*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-gear"></i></span> Automações
            </a>

            <div class="sidebar-section-label">Sistema</div>
            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-gear-fill"></i></span> Configurações (SMTP)
            </a>
            <a href="{{ route('admin.email-templates.index') }}" class="sidebar-link {{ request()->routeIs('admin.email-templates*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-layout-text-window-reverse"></i></span> Modelos de E-mail
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="flex-1 truncate">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Admin</div>
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
        {{-- Topbar --}}
        <div class="topbar">
            <div class="flex items-center gap-3">
                <button class="btn btn-ghost btn-sm" @click="$store.sidebar.toggle()"><i class="bi bi-list fs-5"></i></button>
                <span class="topbar-title">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="topbar-actions">
                @if (session('success'))
                    <div class="badge badge-green"><i class="bi bi-check2"></i> {{ session('success') }}</div>
                @endif
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

        <div class="page-container fade-in">
            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
