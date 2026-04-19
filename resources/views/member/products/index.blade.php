@extends('layouts.member')
@section('title', 'Catálogo')
@section('breadcrumb', 'Catálogo')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-grid-fill me-2"></i> Catálogo de Produtos</h1>
</div>

<div class="grid-courses">
    @forelse ($products as $product)
    @php
        $link = $product->is_enrolled ? route('member.products.show', $product->slug) : ($product->checkout_url ?: route('member.products.show', $product->slug));
        $target = (!$product->is_enrolled && $product->checkout_url) ? '_blank' : '_self';
    @endphp
    <a href="{{ $link }}" target="{{ $target }}" class="course-card {{ $product->is_enrolled ? 'enrolled' : 'locked' }}">
        @if ($product->thumbnail)
            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" class="course-thumb">
        @else
            <div class="course-thumb-placeholder">
                {!! match($product->type) { 'course'=>'<i class="bi bi-mortarboard-fill"></i>', 'ebook'=>'<i class="bi bi-journal-bookmark-fill"></i>', 'download'=>'<i class="bi bi-download"></i>', 'membership'=>'<i class="bi bi-star-fill"></i>', default=>'<i class="bi bi-box-seam"></i>' } !!}
            </div>
        @endif
        
        @if (!$product->is_enrolled)
        <div class="course-lock-overlay">
            <i class="bi bi-lock-fill"></i>
        </div>
        @endif

        <div class="course-body">
            <div class="flex items-center gap-2 mb-2">
                <span class="badge badge-purple" style="box-shadow: 0 2px 5px rgba(0,0,0,0.5);">{{ $product->getTypeLabel() }}</span>
                @if ($product->is_enrolled)
                    <span class="badge badge-green" style="box-shadow: 0 2px 5px rgba(0,0,0,0.5);"><i class="bi bi-check2"></i> Matriculado</span>
                @endif
            </div>
            <h3 class="course-title">{{ $product->title }}</h3>
            @if (!$product->is_enrolled)
                <div class="mb-2">
                    <span class="font-bold text-lg" style="color: var(--primary); text-shadow: 0 1px 3px rgba(0,0,0,0.5);">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </span>
                </div>
            @endif
            <p class="course-desc">{{ $product->description }}</p>
            <div class="course-footer">
                @if ($product->is_enrolled)
                    @if($product->type === 'course')
                        <span class="btn btn-primary"><i class="bi bi-play-fill"></i> Começar</span>
                    @else
                        <span class="btn btn-primary"><i class="bi bi-cloud-arrow-down-fill"></i> Acessar</span>
                    @endif
                @else
                    <span class="btn btn-primary w-full justify-center"><i class="bi bi-cart-fill"></i> Adquirir agora</span>
                @endif
            </div>
        </div>
    </a>
    @empty
    <div class="card" style="grid-column: 1/-1; text-align: center; padding: 60px;">
        <div style="font-size: 3rem; margin-bottom: 16px;"><i class="bi bi-box-seam"></i></div>
        <h3>Nenhum produto disponível ainda</h3>
    </div>
    @endforelse
</div>
@endsection
