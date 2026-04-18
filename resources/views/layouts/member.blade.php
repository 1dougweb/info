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
    @include('layouts.partials.branding')
</head>
<body>

<div class="app-layout" x-data>

    {{-- Sidebar --}}
    <aside class="sidebar" :class="$store.sidebar.open ? 'open' : ''">
        <div class="sidebar-brand">
            @if ($logo = \App\Models\Setting::get('branding_logo'))
                <img src="{{ asset($logo) }}" alt="Logo" style="height: 32px; max-width: 140px; object-fit: contain;">
            @else
                <div class="sidebar-brand-icon"><i class="bi bi-lightning-fill"></i></div>
                <div>
                    <div class="sidebar-brand-name">MembersArea</div>
                    <div class="sidebar-brand-sub">Área de Membros</div>
                </div>
            @endif
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
    </aside>

    {{-- Main --}}
    <main class="main-content">
        <div class="topbar">
            <div class="flex items-center gap-3">
                <button class="btn btn-ghost btn-sm" @click="$store.sidebar.toggle()"><i class="bi bi-list fs-5"></i></button>
                <span class="topbar-title">@yield('breadcrumb', 'Início')</span>
            </div>
            
            <div class="topbar-actions flex items-center gap-4">
                <div x-data="{ open: false }" @click.away="open = false" class="topbar-user">
                    <div class="flex items-center gap-3" @click="open = !open">
                        <div class="sidebar-user-avatar" style="overflow: hidden; width: 36px; height: 36px;">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div style="line-height: 1.2;">
                            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-1);">{{ auth()->user()->name }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-3);">Membro</div>
                        </div>
                        <i class="bi bi-chevron-down text-muted" style="font-size: 0.8rem;"></i>
                    </div>

                    <div x-show="open" 
                         x-transition.opacity.duration.200ms
                         class="dropdown-menu" 
                         style="display: none;">
                        <a href="{{ route('member.profile') }}" class="dropdown-item">
                            <i class="bi bi-person"></i> Meu Perfil
                        </a>
                        <a href="{{ route('member.profile') }}" class="dropdown-item">
                            <i class="bi bi-gear"></i> Configurações
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item text-red">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </button>
                        </form>
                    </div>
                </div>
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
