@extends('layouts.member')
@section('title', 'Minha Área')
@section('breadcrumb', 'Início')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Olá, {{ explode(' ', auth()->user()->name)[0] }}! <i class="bi bi-hand-wave-fill ms-2 text-warning"></i></h1>
        <p class="text-muted mt-2">Continue de onde parou.</p>
    </div>
    <a href="{{ route('member.products.index') }}" class="btn btn-secondary">Ver catálogo</a>
</div>

@if ($enrollmentsWithProgress->isEmpty())
<div class="card" style="text-align: center; padding: 80px 20px;">
    <div style="font-size: 4rem; margin-bottom: 24px; color: var(--text-3);"><i class="bi bi-box-seam"></i></div>
    <h3 style="margin-bottom: 32px; font-weight: 600;">Você ainda não tem nenhum produto ou curso</h3>
    <a href="{{ route('member.products.index') }}" class="btn btn-primary btn-lg">Ver produtos disponíveis</a>
</div>
@else

<div class="grid-courses">
    @foreach ($enrollmentsWithProgress as $item)
    @php $product = $item['product']; @endphp
    <a href="{{ route('member.products.show', $product->slug) }}" class="course-card enrolled">
        @if ($product->thumbnail)
            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" class="course-thumb">
        @else
            <div class="course-thumb-placeholder"><i class="bi bi-box-seam"></i></div>
        @endif
        <div class="course-body">
            <div class="flex items-center gap-2 mb-2">
                <span class="badge badge-purple" style="box-shadow: 0 2px 5px rgba(0,0,0,0.5);">{{ $product->getTypeLabel() }}</span>
                @if ($item['percentage'] === 100)
                    <span class="badge badge-green" style="box-shadow: 0 2px 5px rgba(0,0,0,0.5);"><i class="bi bi-check2"></i> Concluído</span>
                @endif
            </div>
            <h3 class="course-title">{{ $product->title }}</h3>
            
            @if($item['total_lessons'] > 0)
            <div class="progress-wrap" style="background: rgba(255,255,255,0.2);">
                <div class="progress-bar" style="width: {{ $item['percentage'] }}%"></div>
            </div>
            <div class="flex justify-between items-center mt-1 mb-3">
                <span class="progress-text text-white" style="text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $item['completed_lessons'] }}/{{ $item['total_lessons'] }} aulas</span>
                <span class="progress-text font-bold text-white" style="text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $item['percentage'] }}%</span>
            </div>
            @endif

            <div class="course-footer">
                <span class="btn btn-primary w-full">
                    @if($product->type === 'course')
                        <i class="bi {{ $item['percentage'] > 0 ? 'bi-play-circle-fill' : 'bi-play-fill' }}"></i> 
                        {{ $item['percentage'] > 0 ? 'Continuar' : 'Começar' }}
                    @else
                        <i class="bi bi-cloud-arrow-down-fill"></i> Acessar
                    @endif
                </span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif
@endsection
