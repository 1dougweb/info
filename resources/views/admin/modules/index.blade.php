@extends('layouts.admin')
@section('title', 'Módulos — ' . $product->title)
@section('breadcrumb', 'Produtos › Módulos')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-journal-bookmark-fill me-2"></i> Módulos</h1>
        <p class="text-muted text-sm mt-2">{{ $product->title }}</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
</div>

@if (session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

<div class="grid-2" style="gap: 24px; align-items: start;">

    {{-- Modules list --}}
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @forelse ($modules as $module)
        <div class="card" x-data="{ openModule: false, editModule: false, addLesson: false }">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <span class="badge badge-gray">#{{ $module->order + 1 }}</span>
                    <span class="font-semibold">{{ $module->title }}</span>
                    <span class="badge badge-blue">{{ $module->lessons_count }} aulas</span>
                </div>
                <div class="flex gap-2">
                    <button @click="addLesson = !addLesson" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Aula</button>
                    <button @click="editModule = !editModule" class="btn btn-secondary btn-sm"><i class="bi bi-pencil-square"></i></button>
                    <form method="POST" action="{{ route('admin.modules.destroy', [$product, $module]) }}" onsubmit="return confirm('Remover módulo?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>

            {{-- Edit module --}}
            <div x-show="editModule" x-cloak style="padding: 16px 24px; border-bottom: 1px solid var(--border-soft);">
                <form method="POST" action="{{ route('admin.modules.update', [$product, $module]) }}" class="flex gap-3">
                    @csrf @method('PUT')
                    <input type="text" name="title" class="form-control" value="{{ $module->title }}" required style="flex: 1;">
                    <button class="btn btn-primary btn-sm">Salvar</button>
                    <button type="button" @click="editModule=false" class="btn btn-ghost btn-sm"><i class="bi bi-x-lg"></i></button>
                </form>
            </div>

            {{-- Add lesson --}}
            <div x-show="addLesson" x-cloak style="padding: 16px 24px; border-bottom: 1px solid var(--border-soft); background: var(--surface-2);">
                <form method="POST" action="{{ route('admin.modules.lessons.store', [$product, $module]) }}" style="display: flex; flex-direction: column; gap: 12px;">
                    @csrf
                    <div class="grid-2" style="gap: 12px;">
                        <div class="form-group" style="grid-column: 1/-1;">
                            <label class="form-label">Título da aula</label>
                            <input type="text" name="title" class="form-control" required placeholder="Ex: Introdução ao módulo">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-control">
                                <option value="video">Vídeo</option>
                                <option value="text">Texto</option>
                                <option value="file">Arquivo</option>
                                <option value="quiz">Quiz</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">URL do Vídeo</label>
                            <input type="url" name="video_url" class="form-control" placeholder="YouTube / Vimeo / Panda URL">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Duração (segundos)</label>
                            <input type="number" name="duration" class="form-control" placeholder="Ex: 600 = 10min" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_free" value="1"> Aula gratuita (preview)
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Conteúdo / Descrição</label>
                        <textarea name="content" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="addLesson=false" class="btn btn-ghost btn-sm">Cancelar</button>
                        <button class="btn btn-primary">Criar Aula</button>
                    </div>
                </form>
            </div>

            {{-- Lessons list --}}
            <div>
                @foreach ($module->lessons as $lesson)
                <div style="display: flex; align-items: center; gap: 12px; padding: 10px 24px; border-bottom: 1px solid var(--border-soft);" x-data="{ editLesson: false }">
                    <span style="font-size: 1rem;">{!! $lesson->type_icon !!}</span>
                    <div class="flex-1">
                        <div class="text-sm font-semibold">{{ $lesson->title }}</div>
                        <div class="text-xs text-muted">{{ $lesson->formatted_duration }} @if($lesson->is_free)<span class="badge badge-green" style="font-size: 0.6rem;">Free</span>@endif</div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editLesson = !editLesson" class="btn btn-ghost btn-sm"><i class="bi bi-pencil-square"></i></button>
                        <form method="POST" action="{{ route('admin.modules.lessons.destroy', [$product, $module, $lesson]) }}" onsubmit="return confirm('Remover aula?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                {{-- Edit lesson inline --}}
                @endforeach
                @if ($module->lessons->isEmpty())
                <div style="padding: 16px 24px; color: var(--text-3); font-size: 0.85rem;">Nenhuma aula ainda</div>
                @endif
            </div>
        </div>
        @empty
        <div class="card card-body text-center text-muted">Nenhum módulo cadastrado. Crie o primeiro!</div>
        @endforelse
    </div>

    {{-- New module form --}}
    <div class="card" style="position: sticky; top: 80px;">
        <div class="card-header"><h3 class="card-title"><i class="bi bi-plus-lg"></i> Novo Módulo</h3></div>
        <form method="POST" action="{{ route('admin.modules.store', $product) }}">
            @csrf
            <div class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Título do módulo</label>
                    <input type="text" name="title" class="form-control" placeholder="Ex: Módulo 1 — Fundamentos" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descrição <span class="text-faint">(opcional)</span></label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Sobre o que é este módulo"></textarea>
                </div>
                <button class="btn btn-primary btn-block">Criar Módulo</button>
            </div>
        </form>
    </div>
</div>
@endsection
