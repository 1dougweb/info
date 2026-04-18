@extends('layouts.admin')
@section('title', 'Identidade Visual')
@section('breadcrumb', 'Sistema › Identidade Visual')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-palette2 me-2"></i> Identidade Visual</h1>
    <p class="text-muted text-sm mt-2">Personalize a aparência completa da sua plataforma para deixá-la com a cara da sua marca.</p>
</div>

@if (session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.settings.branding.update') }}" enctype="multipart/form-data" x-data="{ preset: '{{ \App\Models\Setting::get('branding_preset', 'default') }}' }">
    @csrf
    <div class="grid-2" style="gap: 24px; align-items: start;">
        
        <div class="flex flex-col gap-6">
            {{-- Section 1: Identity --}}
            <div class="card">
                <div class="card-header"><h3 class="font-semibold">Logotipo e Ícone</h3></div>
                <div class="card-body">
                    <div class="form-group mb-6">
                        <label class="form-label">Logotipo da Plataforma</label>
                        <div class="flex items-center gap-6 mt-2">
                            @if ($logo = \App\Models\Setting::get('branding_logo'))
                                <img src="{{ asset($logo) }}" alt="Logo" style="height: 60px; max-width: 150px; object-fit: contain; background: var(--surface-2); padding: 8px; border-radius: var(--radius);">
                            @else
                                <div style="height: 60px; width: 60px; background: var(--surface-2); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; color: var(--text-3);">
                                    <i class="bi bi-image fs-3"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" name="branding_logo" class="form-control" accept="image/*">
                                <span class="form-hint">Recomendado fundo transparente (PNG/SVG).</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Favicon (Ícone da Aba)</label>
                        <div class="flex items-center gap-6 mt-2">
                            @if ($favicon = \App\Models\Setting::get('branding_favicon'))
                                <img src="{{ asset($favicon) }}" alt="Favicon" style="height: 32px; width: 32px; object-fit: contain; background: var(--surface-2); padding: 4px; border-radius: 4px;">
                            @else
                                <div style="height: 32px; width: 32px; background: var(--surface-2); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--text-3);">
                                    <i class="bi bi-app-indicator"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" name="branding_favicon" class="form-control" accept="image/x-icon,image/png,image/svg+xml">
                                <span class="form-hint">Proporção 1:1 (Ex: 32x32px).</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Theme Presets --}}
            <div class="card">
                <div class="card-header"><h3 class="font-semibold">Tema e Cor Primária</h3></div>
                <div class="card-body">
                    <div class="preset-grid">
                        @php
                            $presets = [
                                'default' => ['label' => 'Laranja', 'color' => '#f97316'],
                                'green'   => ['label' => 'Verde', 'color' => '#22c55e'],
                                'blue'    => ['label' => 'Azul', 'color' => '#3b82f6'],
                                'red'     => ['label' => 'Vermelho', 'color' => '#ef4444'],
                                'purple'  => ['label' => 'Roxo', 'color' => '#a855f7'],
                            ];
                        @endphp
                        @foreach ($presets as $key => $p)
                            <label class="preset-option">
                                <input type="radio" name="branding_preset" value="{{ $key }}" class="hidden" x-model="preset">
                                <div class="preset-card" :class="preset === '{{ $key }}' ? 'active' : ''" :style="preset === '{{ $key }}' ? 'border-color: {{ $p['color'] }}' : ''">
                                    <div class="preset-dot" style="background: {{ $p['color'] }};"></div>
                                    <span class="preset-label">{{ $p['label'] }}</span>
                                </div>
                            </label>
                        @endforeach
                        
                        <label class="preset-option">
                            <input type="radio" name="branding_preset" value="custom" class="hidden" x-model="preset">
                            <div class="preset-card" :class="preset === 'custom' ? 'active' : ''">
                                <div class="preset-dot" style="background: linear-gradient(45deg, #ff0000, #00ff00, #0000ff);"></div>
                                <span class="preset-label">Custom</span>
                            </div>
                        </label>
                    </div>

                    <div class="mt-6 p-4 rounded-lg bg-surface-2 border border-faint" x-show="preset === 'custom'" x-cloak x-transition>
                        <label class="form-label text-xs">Cor Primária Personalizada</label>
                        <div class="flex items-center gap-4 mt-2">
                            <input type="color" name="branding_custom_color" class="form-control-color" value="{{ \App\Models\Setting::get('branding_custom_color', '#f97316') }}">
                            <input type="text" class="form-control flex-1" value="{{ \App\Models\Setting::get('branding_custom_color', '#f97316') }}" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            {{-- Section 3: Immersion & Previews --}}
            <div class="card h-full">
                <div class="card-header"><h3 class="font-semibold">Imersão e Detalhes</h3></div>
                <div class="card-body flex flex-col gap-6">
                    
                    <div class="alert alert-info py-4 px-5 rounded-lg bg-primary-faint border-primary shadow-sm">
                        <div class="flex gap-4">
                            <i class="bi bi-stars text-primary fs-4"></i>
                            <div>
                                <strong class="text-sm block mb-1">Motor de Imersão HSL Ativado</strong>
                                <p class="text-xs leading-relaxed text-muted">A plataforma agora calcula automaticamente matizes de fundo, bordas e superfícies com base na cor do tema. Isso cria uma atmosfera coesa em todas as telas.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Manual Overrides --}}
                    <div class="space-y-6 mt-4">
                        <div class="form-group border-bottom border-faint pb-6">
                            <label class="form-label flex justify-between">
                                Cor de Fundo (Override)
                                <span class="text-xs text-primary">Sugerido por HSL</span>
                            </label>
                            <div class="flex items-center gap-4 mt-2">
                                <input type="color" name="branding_bg_color" class="form-control-color" value="{{ \App\Models\Setting::get('branding_bg_color', '#0a0f0d') }}" disabled style="opacity: 0.5; cursor: not-allowed;">
                                <div class="flex-1 flex flex-col">
                                    <span class="text-xs font-mono text-muted">Automático</span>
                                    <span class="text-xs text-muted italic">O fundo agora segue a matiz do tema para máxima imersão.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group border-bottom border-faint pb-6">
                            <label class="form-label flex justify-between">
                                Texto de Botões
                            </label>
                            <div class="flex items-center gap-4 mt-2">
                                <input type="color" name="branding_btn_text_color" class="form-control-color" value="{{ \App\Models\Setting::get('branding_btn_text_color', '#ffffff') }}">
                                <div class="flex-1 flex flex-col">
                                    <span class="text-xs font-mono">{{ \App\Models\Setting::get('branding_btn_text_color', '#ffffff') }}</span>
                                    <span class="text-xs text-muted">Cor da fonte nos botões principais.</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label flex justify-between">
                                Cor das Etiquetas (Badges)
                            </label>
                            <div class="flex items-center gap-4 mt-2">
                                <input type="color" name="branding_badge_color" class="form-control-color" value="{{ \App\Models\Setting::get('branding_badge_color', '#22c55e') }}">
                                <div class="flex-1 flex flex-col">
                                    <span class="text-xs font-mono">{{ \App\Models\Setting::get('branding_badge_color', '#22c55e') }}</span>
                                    <span class="text-xs text-muted">Define a cor da etiqueta primária.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Theme Preview --}}
                    <div class="mt-auto border border-faint rounded-xl p-6 bg-custom-preview" style="background: var(--surface-1); box-shadow: inset 0 0 20px rgba(0,0,0,0.5);">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-muted mb-4">Preview de Strokes (Bordas)</h4>
                        <div class="flex flex-wrap gap-3">
                            <div class="badge badge-primary">Badge Temático</div>
                            <div class="badge" style="border: 1px solid var(--border); background: var(--surface-2);">Stroke Imersivo</div>
                            <div class="badge" style="border: 1px solid var(--border-soft); background: var(--surface-3);">Soft Stroke</div>
                        </div>
                        <div class="mt-4 p-3 rounded bg-surface-2 border border-primary-faint text-xs text-muted">
                            <i class="bi bi-info-circle me-1"></i> Notou? As bordas acima adaptam-se ao tema.
                        </div>
                    </div>
                </div>
                <div class="card-footer p-6 border-top border-faint">
                    <button type="submit" class="btn btn-primary w-full btn-lg shadow-lg">
                        <i class="bi bi-check-all me-1"></i> Aplicar Identidade Visual
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.preset-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.preset-option {
    cursor: pointer;
}
.preset-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: var(--radius-lg);
    background: var(--surface-1);
    border: 2px solid var(--border-soft);
    transition: all 0.2s ease;
}
.preset-card.active {
    background: var(--primary-faint) !important;
    border-color: var(--primary) !important;
}
.preset-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    flex-shrink: 0;
}
.preset-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-2);
}
.form-control-color {
    width: 64px;
    height: 48px;
    border: 2px solid var(--border-soft);
    border-radius: var(--radius);
    padding: 4px;
    background: var(--surface-2);
    cursor: pointer;
    transition: all 0.2s;
}
.form-control-color:hover {
    border-color: var(--primary);
}
.form-control-color::-webkit-color-swatch {
    border-radius: 4px;
    border: none;
}
.border-bottom { border-bottom: 1px solid; }
</style>
@endsection
