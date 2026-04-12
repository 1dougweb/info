@extends('layouts.admin')
@section('title', 'Novo Produto')
@section('breadcrumb', 'Produtos › Novo')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-box-seam me-2"></i> Novo Produto</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
</div>

<div class="card" style="max-width: 800px;">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body" style="display: flex; flex-direction: column; gap: 20px;">

            @if ($errors->any())
            <div class="alert alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <div class="grid-2" style="gap: 16px;">
                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Título *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Ex: Curso de Marketing Digital">
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo *</label>
                    <select name="type" class="form-control" required>
                        <option value="course"   {{ old('type') === 'course'     ? 'selected' : '' }}>Curso</option>
                        <option value="ebook"    {{ old('type') === 'ebook'      ? 'selected' : '' }}>E-book</option>
                        <option value="download" {{ old('type') === 'download'   ? 'selected' : '' }}>Download</option>
                        <option value="membership"{{ old('type') === 'membership' ? 'selected' : '' }}>Membership</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Preço (R$) *</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', 0) }}" step="0.01" min="0" required>
                </div>
                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">URL Oficial de Compra no Checkout</label>
                    <input type="url" name="checkout_url" class="form-control" value="{{ old('checkout_url') }}" placeholder="Ex: https://pay.hotmart.com/X1234">
                    <span class="form-hint">Onde os alunos vão clicar para comprar caso não tenham acesso.</span>
                </div>
                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Descrição curta</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Descrição para cards e listagens">{{ old('description') }}</textarea>
                </div>

                <div class="form-group" style="grid-column: 1/-1;">
                    <label class="form-label">Conteúdo / Página de apresentação</label>
                    <textarea name="content" class="form-control" rows="6" placeholder="Conteúdo HTML ou texto da página do produto">{{ old('content') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Thumbnail / Capa</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        <option value="draft"     {{ old('status','draft') === 'draft'     ? 'selected':'' }}>Rascunho</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected':'' }}>Publicado</option>
                        <option value="archived"  {{ old('status') === 'archived'  ? 'selected':'' }}>Arquivado</option>
                    </select>
                </div>

                <div style="grid-column: 1/-1; border-top: 1px solid var(--border-soft); padding-top: 20px;">
                    <h4 class="mb-4" style="color: var(--text-2); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">IDs nos checkouts (opcional)</h4>
                    <div class="grid-3" style="gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">ID Hotmart</label>
                            <input type="text" name="checkout_hotmart_id" class="form-control" value="{{ old('checkout_hotmart_id') }}" placeholder="ID do produto na Hotmart">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ID Cakto</label>
                            <input type="text" name="checkout_cakto_id" class="form-control" value="{{ old('checkout_cakto_id') }}" placeholder="ID do produto na Cakto">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ID Wikify</label>
                            <input type="text" name="checkout_wikify_id" class="form-control" value="{{ old('checkout_wikify_id') }}" placeholder="ID do produto na Wikify">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Criar Produto</button>
            </div>
        </div>
    </form>
</div>
@endsection
