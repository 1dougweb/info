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
    @include('layouts.partials.branding')
</head>
<body>

<div class="app-layout" x-data>

    {{-- Backdrop (Mobile/Tablet) --}}
    <div class="sidebar-backdrop" 
         x-show="$store.sidebar.open && window.innerWidth <= 1024" 
         x-transition:enter="fade-in"
         x-transition:leave="fade-out"
         x-cloak
         @click="$store.sidebar.close()">
    </div>

    {{-- Sidebar --}}
    <aside class="sidebar no-transition" 
           x-data="{ ready: false }"
           x-init="setTimeout(() => ready = true, 100)"
           :class="{ 
               'open': $store.sidebar.open, 
               'collapsed': !$store.sidebar.open,
               'no-transition': !ready
           }">
        <div class="sidebar-brand">
            @if ($logo = \App\Models\Setting::get('branding_logo'))
                <img src="{{ asset($logo) }}" alt="Logo" style="height: 32px; max-width: 140px; object-fit: contain;">
            @else
                <div class="sidebar-brand-icon"><i class="bi bi-lightning-fill"></i></div>
                <div>
                    <div class="sidebar-brand-name">MembersArea</div>
                    <div class="sidebar-brand-sub">Painel Admin</div>
                </div>
            @endif
        </div>

        @php
            $activeGroup = '';
            if (request()->routeIs('admin.dashboard')) $activeGroup = 'inicio';
            elseif (request()->routeIs('admin.products*') || request()->routeIs('admin.users*')) $activeGroup = 'gestao';
            elseif (request()->routeIs('admin.webhooks*') || request()->routeIs('admin.automations*')) $activeGroup = 'integracoes';
            elseif (request()->routeIs('admin.settings*') || request()->routeIs('admin.email-templates*')) $activeGroup = 'sistema';
        @endphp

        <nav class="sidebar-nav" x-data="{ activeDropdown: '{{ $activeGroup }}' }">
            {{-- Group: Início --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               @click="activeDropdown = ''">
                <span class="sidebar-icon"><i class="bi bi-house-door-fill"></i></span>
                Dashboard
            </a>

            {{-- Group: Gestão --}}
            <div class="sidebar-group">
                <button @click="activeDropdown = activeDropdown === 'gestao' ? '' : 'gestao'" 
                        class="sidebar-link sidebar-dropdown-trigger {{ $activeGroup === 'gestao' ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-box-seam-fill"></i></span>
                    Gestão
                    <i class="bi sidebar-chevron" :class="activeDropdown === 'gestao' ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </button>
                <div x-show="activeDropdown === 'gestao'" x-cloak class="sidebar-submenu">
                    <a href="{{ route('admin.products.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                        Produtos
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        Membros
                    </a>
                </div>
            </div>

            {{-- Group: Integrações --}}
            <div class="sidebar-group">
                <button @click="activeDropdown = activeDropdown === 'integracoes' ? '' : 'integracoes'" 
                        class="sidebar-link sidebar-dropdown-trigger {{ $activeGroup === 'integracoes' ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-plug-fill"></i></span>
                    Integrações
                    <i class="bi sidebar-chevron" :class="activeDropdown === 'integracoes' ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </button>
                <div x-show="activeDropdown === 'integracoes'" x-cloak class="sidebar-submenu">
                    <a href="{{ route('admin.webhooks.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.webhooks*') ? 'active' : '' }}">
                        Webhooks
                    </a>
                    <a href="{{ route('admin.automations.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.automations*') ? 'active' : '' }}">
                        Automações
                    </a>
                </div>
            </div>

            {{-- Group: Configurações --}}
            <div class="sidebar-group">
                <button @click="activeDropdown = activeDropdown === 'sistema' ? '' : 'sistema'" 
                        class="sidebar-link sidebar-dropdown-trigger {{ $activeGroup === 'sistema' ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-gear-fill"></i></span>
                    Configurações
                    <i class="bi sidebar-chevron" :class="activeDropdown === 'sistema' ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </button>
                <div x-show="activeDropdown === 'sistema'" x-cloak class="sidebar-submenu">
                    <a href="{{ route('admin.settings.branding.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.settings.branding*') ? 'active' : '' }}">
                        Identidade Visual
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                        Servidor SMTP
                    </a>
                    <a href="{{ route('admin.email-templates.index') }}" class="sidebar-link sidebar-sublink {{ request()->routeIs('admin.email-templates*') ? 'active' : '' }}">
                        Modelos de E-mail
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    {{-- Main --}}
    <main class="main-content">
        {{-- Topbar --}}
        <div class="topbar">
            <div class="flex items-center gap-3">
                <button class="btn btn-ghost btn-sm" @click="$store.sidebar.toggle()"><i class="bi bi-list fs-5"></i></button>
                <span class="topbar-title">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="topbar-actions flex items-center gap-4">
                @if (session('success'))
                    <div class="badge badge-green"><i class="bi bi-check2"></i> {{ session('success') }}</div>
                @endif
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
                            <div style="font-size: 0.7rem; color: var(--text-3);">Admin</div>
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
                        <a href="{{ route('admin.settings.branding.index') }}" class="dropdown-item">
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

        <div class="page-container fade-in">
            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
