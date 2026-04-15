@extends('layouts.member')
@section('title', $lesson->title)
@section('breadcrumb', $product->title . ' › ' . $lesson->title)

@section('content')
<div class="player-layout" style="margin: -32px;">

    {{-- Video & content --}}
    <div class="player-main">

        {{-- Video --}}
        @if ($lesson->type === 'video' && $lesson->video_url)
        <div class="video-container">
            @php
                $url = $lesson->video_url;
                // Detect embed type
                if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
                    preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m);
                    $embedUrl = 'https://www.youtube.com/embed/' . ($m[1] ?? '') . '?autoplay=1&rel=0';
                } elseif (str_contains($url, 'vimeo.com')) {
                    preg_match('/vimeo\.com\/(\d+)/', $url, $m);
                    $embedUrl = 'https://player.vimeo.com/video/' . ($m[1] ?? '') . '?autoplay=1';
                } else {
                    $embedUrl = $url; // Panda or direct embed
                }
            @endphp
            <iframe src="{{ $embedUrl }}" allowfullscreen allow="autoplay; fullscreen"></iframe>
        </div>
        @elseif ($lesson->type === 'text')
        <div style="background: #000; padding: 24px; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;">
            <span style="font-size: 4rem; color: #fff;"><i class="bi bi-file-text"></i></span>
        </div>
        @elseif ($lesson->type === 'file')
        <div style="background: #000; padding: 24px; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 16px;">
            <span style="font-size: 4rem; color: #fff;"><i class="bi bi-paperclip"></i></span>
            @if ($lesson->file_path)
            <a href="{{ Storage::disk('public')->url($lesson->file_path) }}" download class="btn btn-primary"><i class="bi bi-download"></i> Baixar arquivo</a>
            @endif
        </div>
        @endif

        {{-- Lesson content --}}
        <div class="player-content">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 style="font-size: 1.4rem; font-weight: 700;">{{ $lesson->title }}</h1>
                    <div class="text-muted text-sm mt-1">
                        {!! $lesson->type_icon !!} {{ ucfirst($lesson->type) }}
                        @if ($lesson->duration) · {{ $lesson->formatted_duration }} @endif
                    </div>
                </div>

                {{-- Complete button --}}
                <div x-data="{ completed: {{ in_array($lesson->id, $progress) ? 'true' : 'false' }} }">
                    <button
                        @click="
                            fetch('{{ route('member.lesson.complete', $lesson) }}', {
                                method: 'POST',
                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                            }).then(()=>{ completed=true; $store.toast.add('Aula marcada como concluída!'); })
                        "
                        :class="completed ? 'btn btn-primary' : 'btn btn-secondary'"
                        :disabled="completed"
                    >
                        <span x-show="!completed">Marcar como concluída</span>
                        <span x-show="completed" x-cloak><i class="bi bi-check2"></i> Concluída</span>
                    </button>
                </div>
            </div>

            @if ($lesson->content)
            <div class="card">
                <div class="card-body" style="color: var(--text-2); line-height: 1.8;">
                    {!! nl2br(e($lesson->content)) !!}
                </div>
            </div>
            @endif

            {{-- Navigation --}}
            <div class="flex justify-between mt-8">
                <div>
                    @if ($prevLesson)
                        <a href="{{ route('member.player', [$product->slug, $prevLesson->id]) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Aula Anterior
                        </a>
                    @else
                        <a href="{{ route('member.products.show', $product->slug) }}" class="btn btn-secondary">
                            <i class="bi bi-box-seam"></i> Voltar ao produto
                        </a>
                    @endif
                </div>

                <div>
                    @if ($nextLesson)
                        <a href="{{ route('member.player', [$product->slug, $nextLesson->id]) }}" class="btn btn-primary">
                            Próxima Aula <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Comments Section --}}
            <div class="mt-8 pt-8" style="border-top: 1px solid var(--border-soft);">
                <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 24px;"><i class="bi bi-chat-left-text"></i> Dúvidas e Comentários ({{ $lesson->comments->count() }})</h3>
                
                {{-- Add comment form --}}
                <div class="card mb-6">
                    <form method="POST" action="{{ route('comments.store', $lesson) }}" class="card-body">
                        @csrf
                        <textarea name="content" class="form-control" rows="3" placeholder="Escreva sua dúvida ou comentário..." required></textarea>
                        <div class="flex justify-end mt-3">
                            <button class="btn btn-primary">Enviar Comentário</button>
                        </div>
                    </form>
                </div>

                {{-- Comments list --}}
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    @foreach ($lesson->comments as $comment)
                    <div class="comment-thread" x-data="{ replyMode: false }">
                        {{-- Parent Comment --}}
                        <div class="flex gap-4">
                            <div style="width: 40px; height: 40px; background: var(--surface-3); border-radius: 50%; display: grid; place-items: center; font-weight: bold; flex-shrink: 0; color: var(--text-2);">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold" style="font-size: 0.95rem;">
                                            {{ $comment->user->name }}
                                            @if($comment->user->is_admin) <span class="badge badge-purple" style="font-size: 0.6rem; margin-left: 4px;">Admin</span> @endif
                                        </div>
                                        <div class="text-xs text-muted mt-1">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                    @if ($comment->user_id === Auth::id() || Auth::user()->is_admin)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Apagar este comentário?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-ghost btn-sm text-red-500"><i class="bi bi-trash"></i></button>
                                    </form>
                                    @endif
                                </div>
                                <div class="mt-2" style="font-size: 0.9rem; color: var(--text-2); line-height: 1.5;">
                                    {!! nl2br(e($comment->content)) !!}
                                </div>
                                <button @click="replyMode = !replyMode" class="btn btn-ghost btn-sm mt-2 p-0 text-muted" style="font-size: 0.8rem;"><i class="bi bi-reply"></i> Responder</button>
                                
                                {{-- Reply Form --}}
                                <div x-show="replyMode" x-cloak class="mt-3">
                                    <form method="POST" action="{{ route('comments.store', $lesson) }}">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <textarea name="content" class="form-control mb-2" rows="2" placeholder="Sua resposta..." required></textarea>
                                        <div class="flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm">Responder</button>
                                            <button type="button" @click="replyMode=false" class="btn btn-secondary btn-sm">Cancelar</button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Replies List --}}
                                @if ($comment->replies->count() > 0)
                                <div class="mt-4" style="display: flex; flex-direction: column; gap: 16px; border-left: 2px solid var(--border-soft); padding-left: 16px;">
                                    @foreach ($comment->replies as $reply)
                                    <div class="flex gap-3">
                                        <div style="width: 32px; height: 32px; background: var(--surface-3); border-radius: 50%; display: grid; place-items: center; font-size: 0.8rem; font-weight: bold; flex-shrink: 0; color: var(--text-2);">
                                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="font-semibold" style="font-size: 0.85rem;">
                                                        {{ $reply->user->name }}
                                                        @if($reply->user->is_admin) <span class="badge badge-purple" style="font-size: 0.5rem; margin-left: 4px;">Admin</span> @endif
                                                    </div>
                                                    <div class="text-xs text-muted">{{ $reply->created_at->diffForHumans() }}</div>
                                                </div>
                                                @if ($reply->user_id === Auth::id() || Auth::user()->is_admin)
                                                <form method="POST" action="{{ route('comments.destroy', $reply) }}" onsubmit="return confirm('Apagar esta resposta?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-ghost btn-sm text-red-500" style="padding: 2px 4px;"><i class="bi bi-trash"></i></button>
                                                </form>
                                                @endif
                                            </div>
                                            <div class="mt-1" style="font-size: 0.85rem; color: var(--text-2); line-height: 1.4;">
                                                {!! nl2br(e($reply->content)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @if ($lesson->comments->isEmpty())
                        <div class="text-center text-muted" style="padding: 32px 0; border: 1px dashed var(--border-soft); border-radius: 8px;">
                            <i class="bi bi-chat-square-dots fs-3"></i>
                            <p class="mt-2">Ninguém comentou ainda. Seja o primeiro a deixar sua dúvida!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="player-sidebar">
        <div class="player-sidebar-header">
            {{ $product->title }}
        </div>
        @foreach ($modules as $module)
        <div x-data="{ open: {{ $module->lessons->contains('id', $lesson->id) ? 'true' : 'false' }} }" class="module-group">
            <button class="module-toggle" @click="open = !open">
                <span style="font-size: 0.85rem;">{{ $module->title }}</span>
                <span x-html="open ? '<i class=\'bi bi-chevron-up\'></i>' : '<i class=\'bi bi-chevron-down\'></i>'" style="color: var(--text-3); font-size: 0.7rem;"></span>
            </button>
            <div x-show="open" x-cloak>
                @foreach ($module->lessons as $ml)
                @php $isCompleted = in_array($ml->id, $progress); @endphp
                <a href="{{ route('member.player', [$product->slug, $ml->id]) }}"
                   class="lesson-item {{ $ml->id === $lesson->id ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                    <div class="lesson-check">{!! $isCompleted ? '<i class="bi bi-check2"></i>' : '' !!}</div>
                    <span style="font-size: 0.8rem; line-height: 1.4;">{{ $ml->title }}</span>
                    <span style="font-size: 0.8rem; flex-shrink: 0;">{!! $ml->type_icon !!}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
