@extends('layouts.admin')
@section('title', 'Editar Produto')
@section('breadcrumb', 'Produtos › Editar')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-pencil-square me-2"></i> Editar: {{ $product->title }}</h1>
    <div class="flex gap-3">
        <a href="{{ route('admin.modules.index', $product) }}" class="btn btn-secondary"><i class="bi bi-journal-bookmark-fill"></i> Módulos ({{ $product->modules->count() }})</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left"></i> Voltar</a>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body" style="display: flex; flex-direction: column; gap: 20px;">

            @if ($errors->any())
            <div class="alert alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success"><i class="bi bi-check2"></i> {{ session('success') }}</div>
            @endif

            <div class="grid-2" style="gap: 16px;">
                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Título *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $product->title) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo *</label>
                    <select name="type" class="form-control" required>
                        @foreach (['course' => 'Curso', 'ebook' => 'E-book', 'download' => 'Download', 'membership' => 'Membership'] as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $product->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Preço (R$) *</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">URL Oficial de Compra no Checkout</label>
                    <input type="url" name="checkout_url" class="form-control" value="{{ old('checkout_url', $product->checkout_url) }}" placeholder="Ex: https://pay.hotmart.com/X1234">
                    <span class="form-hint">Onde os alunos vão clicar para comprar caso não tenham acesso.</span>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Descrição curta</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Conteúdo / Página de apresentação</label>
                    <textarea name="content" class="form-control" rows="6">{{ old('content', $product->content) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Nova Thumbnail</label>
                    @if ($product->thumbnail)
                    <img src="{{ asset('storage/'.$product->thumbnail) }}" style="width: 120px; height: 68px; object-fit: cover; border-radius: 6px; margin-bottom: 8px;">
                    @endif
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        @foreach (['draft' => 'Rascunho', 'published' => 'Publicado', 'archived' => 'Arquivado'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $product->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="grid-column: 1/-1; border-top: 1px solid var(--border-soft); padding-top: 20px;">
                    <h4 class="mb-4" style="color: var(--text-2); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">IDs nos checkouts</h4>
                    <div class="grid-3" style="gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">ID Hotmart</label>
                            <input type="text" name="checkout_hotmart_id" class="form-control" value="{{ old('checkout_hotmart_id', $product->checkout_hotmart_id) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ID Cakto</label>
                            <input type="text" name="checkout_cakto_id" class="form-control" value="{{ old('checkout_cakto_id', $product->checkout_cakto_id) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ID Wikify</label>
                            <input type="text" name="checkout_wikify_id" class="form-control" value="{{ old('checkout_wikify_id', $product->checkout_wikify_id) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </div>
    </form>
</div>
@endsection
