@extends('layouts.admin')
@section('title', 'Produtos')
@section('breadcrumb', 'Produtos')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-box-seam me-2"></i> Produtos</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo Produto</a>
</div>

<div class="card">
    <div class="table-wrap" style="border: none; border-radius: 0;">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">Imagem</th>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Preço</th>
                    <th>Matrículas</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr>
                    <td>
                        <img src="{{ $product->thumbnail_url }}" style="width: 60px; height: 34px; object-fit: cover; border-radius: 4px; background: var(--surface-3);">
                    </td>
                    <td>
                        <div class="font-semibold">{{ $product->title }}</div>
                        <div class="text-xs text-muted">{{ $product->slug }}</div>
                    </td>
                    <td><span class="badge badge-purple">{{ $product->getTypeLabel() }}</span></td>
                    <td class="font-semibold text-primary">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                    <td>{{ $product->enrollments_count }}</td>
                    <td>
                        @if ($product->status === 'published')
                            <span class="badge badge-green">Publicado</span>
                        @elseif ($product->status === 'draft')
                            <span class="badge badge-yellow">Rascunho</span>
                        @else
                            <span class="badge badge-gray">Arquivado</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.modules.index', $product) }}" class="btn btn-ghost btn-sm" title="Módulos"><i class="bi bi-journal-bookmark-fill"></i></a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary btn-sm"><i class="bi bi-pencil-square"></i> Editar</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Remover produto?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted p-6">Nenhum produto cadastrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($products->hasPages())
    <div class="card-body" style="padding: 16px 24px;">{{ $products->links() }}</div>
    @endif
</div>
@endsection
