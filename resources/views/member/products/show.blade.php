@extends('layouts.member')
@section('title', $product->title)
@section('breadcrumb', $product->title)

@section('content')
<div class="page-header">
    <div class="flex items-center gap-4">
        <a href="{{ route('member.products.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left fs-5"></i></a>
        <div>
            <h1 class="page-title">{{ $product->title }}</h1>
            <span class="badge badge-purple mt-2">{{ $product->getTypeLabel() }}</span>
        </div>
    </div>
</div>

<div class="grid-2" style="gap: 32px; align-items: start;">

    {{-- Left: product info --}}
    <div style="display: flex; flex-direction: column; gap: 20px;">
        @if ($product->thumbnail)
        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" style="border-radius: var(--radius-lg); width: 100%; aspect-ratio: 16/9; object-fit: cover;">
        @endif

        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Sobre este produto</h3>
                <p class="text-muted">{{ $product->description }}</p>
                @if ($product->content)
                <div style="margin-top: 16px; color: var(--text-2); font-size: 0.9rem;">
                    {!! nl2br(e($product->content)) !!}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: CTA + module list --}}
    <div style="display: flex; flex-direction: column; gap: 20px;">

        @if (!$isEnrolled)
        <div class="card" style="border-color: var(--border);">
            <div class="card-body text-center">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary); margin-bottom: 8px;">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </div>
                <p class="text-muted text-sm mb-6">Compre através do botão abaixo para ter acesso imediato.</p>
                @if($product->checkout_url)
                <a href="{{ $product->checkout_url }}" target="_blank" class="btn btn-primary btn-lg mb-6"><i class="bi bi-cart2"></i> Comprar Agora</a>
                <br>
                @endif
                <div class="alert alert-info"><i class="bi bi-info-circle-fill"></i> Após a compra, o acesso é liberado automaticamente.</div>
            </div>
        </div>
        @else
        <div class="card" style="border-color: var(--primary);">
            <div class="card-body text-center">
                <div style="color: var(--primary); font-size: 1.5rem; margin-bottom: 8px;"><i class="bi bi-check-circle-fill me-2"></i> Você tem acesso</div>
                <p class="text-muted text-sm">Continue seu aprendizado abaixo.</p>

                <div class="mt-6" style="text-align: left;">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold">Progresso</span>
                        <span class="text-sm font-semibold text-primary">{{ $percentage }}%</span>
                    </div>
                    <div class="progress-wrap">
                        <div class="progress-bar" style="width: {{ $percentage }}%;"></div>
                    </div>
                    <div class="text-xs text-muted">{{ $completedLessons }} de {{ $totalLessons }} aulas concluídas</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modules --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Conteúdo do produto</h3>
                <span class="text-muted text-sm">{{ $modules->count() }} módulos</span>
            </div>
            @forelse ($modules as $module)
            <div x-data="{ open: true }" class="module-group">
                <button class="module-toggle" @click="open = !open">
                    <span>{{ $module->title }}</span>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-gray">{{ $module->lessons->count() }} aulas</span>
                        <span x-html="open ? '<i class=\'bi bi-chevron-up\'></i>' : '<i class=\'bi bi-chevron-down\'></i>'" style="color: var(--text-3);"></span>
                    </div>
                </button>
                <div x-show="open" x-cloak>
                    @foreach ($module->lessons as $lesson)
                    @php $isCompleted = in_array($lesson->id, $progress); @endphp

                    @if ($isEnrolled || $lesson->is_free)
                    <a href="{{ route('member.player', [$product->slug, $lesson->id]) }}"
                       class="lesson-item {{ $isCompleted ? 'completed' : '' }}">
                        <div class="lesson-check">{!! $isCompleted ? '<i class="bi bi-check2"></i>' : '' !!}</div>
                        <div class="flex-1">
                            <div style="font-size: 0.85rem;">{{ $lesson->title }}</div>
                            <div class="text-xs text-muted">{{ $lesson->formatted_duration }}</div>
                        </div>
                        <span style="font-size: 0.85rem;">{!! $lesson->type_icon !!}</span>
                        @if ($lesson->is_free)
                        <span class="badge badge-green" style="font-size: 0.6rem;">Grátis</span>
                        @endif
                    </a>
                    @else
                    <div class="lesson-item" style="opacity: 0.4; cursor: not-allowed;">
                        <div class="lesson-check"><i class="bi bi-lock-fill"></i></div>
                        <div class="flex-1 text-sm">{{ $lesson->title }}</div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @empty
            <div class="card-body text-muted">Conteúdo em breve</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
