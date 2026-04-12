@extends('layouts.member')
@section('title', 'Catálogo')
@section('breadcrumb', 'Catálogo')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-grid-fill me-2"></i> Catálogo de Produtos</h1>
</div>

<div class="grid-courses">
    @forelse ($products as $product)
    <div class="course-card">
        @if ($product->thumbnail)
            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" class="course-thumb">
        @else
            <div class="course-thumb-placeholder">
                {!! match($product->type) { 'course'=>'<i class="bi bi-mortarboard-fill"></i>', 'ebook'=>'<i class="bi bi-journal-bookmark-fill"></i>', 'download'=>'<i class="bi bi-download"></i>', 'membership'=>'<i class="bi bi-star-fill"></i>', default=>'<i class="bi bi-box-seam"></i>' } !!}
            </div>
        @endif
        <div class="course-body">
            <div class="flex items-center gap-2 mb-2">
                <span class="badge badge-purple">{{ $product->getTypeLabel() }}</span>
                @if ($product->is_enrolled)
                    <span class="badge badge-green"><i class="bi bi-check2"></i> Matriculado</span>
                @endif
            </div>
            <h3 class="course-title">{{ $product->title }}</h3>
            <p class="course-desc">{{ $product->description }}</p>
            <div class="course-footer">
                @if ($product->is_enrolled)
                    <a href="{{ route('member.products.show', $product->slug) }}" class="btn btn-primary">Acessar</a>
                @else
                    <a href="{{ route('member.products.show', $product->slug) }}" class="btn btn-secondary">Ver detalhes</a>
                    <span class="font-semibold text-primary">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column: 1/-1; text-align: center; padding: 60px;">
        <div style="font-size: 3rem; margin-bottom: 16px;"><i class="bi bi-box-seam"></i></div>
        <h3>Nenhum produto disponível ainda</h3>
    </div>
    @endforelse
</div>
@endsection
