@extends('layouts.admin')
@section('title', 'Automações')
@section('breadcrumb', 'Integrações › Automações')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-gear me-2"></i> Automações</h1>
    <button x-data @click="$dispatch('open-create-modal')" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nova Automação</button>
</div>

@if(session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

@foreach($products as $product)
<div class="card mb-6" x-data="{ open: false }">
    <div class="card-header bg-surface-2" @click="open = !open" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
        <div class="flex items-center gap-3">
            @if ($product->thumbnail)
                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
            @else
                <div style="width: 40px; height: 40px; border-radius: 6px; background: var(--surface-3); display: grid; place-items: center;">
                    <i class="bi bi-box-seam text-muted"></i>
                </div>
            @endif
            <div>
                <h3 class="card-title m-0" style="font-size: 1.1rem;">{{ $product->title }}</h3>
                <span class="text-xs text-muted">Automações vinculadas a este produto</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click.stop="$dispatch('open-create-modal', { productId: {{ $product->id }} })" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg"></i> Nova Automação neste Produto
            </button>
            <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'" style="color: var(--text-3);"></i>
        </div>
    </div>

    <div x-show="open" x-cloak>
    @php
        $productAutomations = $automations->where('product_id', $product->id);
    @endphp

    @if($productAutomations->isNotEmpty())
    <div class="table-wrap" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>Nome / Trigger</th>
                    <th>Ação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productAutomations as $auto)
                <tr>
                    <td>
                        <div class="font-semibold">{{ $auto->name }}</div>
                        <div class="text-xs mt-1">
                            <span class="badge badge-yellow">{{ $auto->getTriggerLabel() }}</span>
                            <span class="badge badge-blue ms-1" style="text-transform: capitalize;">{{ $auto->source === 'any' ? 'Qualquer' : "Webhook: ".$auto->source }}</span>
                        </div>
                        @if($auto->source_product_id)
                        <div class="text-xs text-muted mt-1"><i class="bi bi-tag"></i> ID Externo: {{ $auto->source_product_id }}</div>
                        @endif
                    </td>
                    <td><span class="badge badge-purple">{{ $auto->getActionLabel() }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.automations.toggle', $auto) }}">
                            @csrf
                            <button class="badge {{ $auto->is_active ? 'badge-green' : 'badge-gray' }}" style="cursor: pointer; border: none;">
                                {!! $auto->is_active ? '<i class="bi bi-circle-fill me-1"></i> Ativo' : '<i class="bi bi-circle me-1"></i> Inativo' !!}
                            </button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.automations.destroy', $auto) }}" onsubmit="return confirm('Remover automação?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="card-body text-center text-muted py-8">
        <i class="bi bi-diagram-3 fs-2" style="opacity: 0.5;"></i>
        <p class="mt-2 text-sm">Nenhuma automação configurada para este produto.<br>Clique no botão acima para adicionar.</p>
    </div>
    @endif
    </div>
</div>
@endforeach

@php
    $globalAutomations = $automations->whereNull('product_id');
@endphp

@if($globalAutomations->isNotEmpty())
<div class="card mb-8" x-data="{ open: false }">
    <div class="card-header bg-surface-2 border-bottom" @click="open = !open" style="cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
        <div class="flex items-center gap-2">
            <i class="bi bi-globe2 text-muted fs-4"></i>
            <div>
                <h3 class="card-title m-0">Automações Globais / Sem Produto</h3>
                <span class="text-xs text-muted">Automações disparadas independentemente do produto interno</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click.stop="$dispatch('open-create-modal', { productId: '' })" class="btn btn-sm btn-secondary">
                <i class="bi bi-plus-lg"></i> Adicionar
            </button>
            <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'" style="color: var(--text-3);"></i>
        </div>
    </div>
    
    <div x-show="open" x-cloak>
    <div class="table-wrap" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>Nome / Trigger</th>
                    <th>Ação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($globalAutomations as $auto)
                <tr>
                    <td>
                        <div class="font-semibold">{{ $auto->name }}</div>
                        <div class="text-xs mt-1">
                            <span class="badge badge-yellow">{{ $auto->getTriggerLabel() }}</span>
                            <span class="badge badge-blue ms-1" style="text-transform: capitalize;">{{ $auto->source === 'any' ? 'Qualquer fonte' : "Webhook: ".$auto->source }}</span>
                        </div>
                    </td>
                    <td><span class="badge badge-purple">{{ $auto->getActionLabel() }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.automations.toggle', $auto) }}">
                            @csrf
                            <button class="badge {{ $auto->is_active ? 'badge-green' : 'badge-gray' }}" style="cursor: pointer; border: none;">
                                {!! $auto->is_active ? '<i class="bi bi-circle-fill me-1"></i> Ativo' : '<i class="bi bi-circle me-1"></i> Inativo' !!}
                            </button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.automations.destroy', $auto) }}" onsubmit="return confirm('Remover automação?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
</div>
@endif


{{-- Cron / Scheduled Tasks Panel --}}
@php
    $scheduledTasks  = \App\Models\ScheduledTask::with('automation')->where('status', 'pending')->orderBy('execute_at')->limit(20)->get();
    $totalPending    = \App\Models\ScheduledTask::where('status', 'pending')->count();
    $totalProcessed  = \App\Models\ScheduledTask::where('status', 'processed')->count();
    $totalFailed     = \App\Models\ScheduledTask::where('status', 'failed')->count();
@endphp

<div class="card mb-8">
    <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 class="card-title" style="font-size: 1rem; margin:0;">
                <i class="bi bi-clock-history me-2"></i> Tarefas Agendadas (Cron)
            </h2>
            <p class="text-xs text-muted mt-1" style="margin:0;">
                Automações com delay aguardando execução via <code>php artisan automations:process</code>
            </p>
        </div>
        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <span class="badge badge-yellow" title="Pendentes"><i class="bi bi-hourglass-split me-1"></i> {{ $totalPending }} pendente(s)</span>
            <span class="badge badge-green" title="Processadas"><i class="bi bi-check-circle me-1"></i> {{ $totalProcessed }} processada(s)</span>
            @if ($totalFailed > 0)
            <span class="badge badge-red" title="Com erro"><i class="bi bi-x-circle me-1"></i> {{ $totalFailed }} erro(s)</span>
            @endif
            <form method="POST" action="{{ route('admin.cron.run') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" title="Executa php artisan automations:process agora">
                    <i class="bi bi-play-fill me-1"></i> Executar Agora
                </button>
            </form>
        </div>
    </div>

    @if ($scheduledTasks->isNotEmpty())
    <div class="table-wrap" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuário</th>
                    <th>Automação</th>
                    <th>Ação</th>
                    <th>Execução em</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scheduledTasks as $task)
                <tr>
                    <td class="text-xs text-muted">{{ $task->id }}</td>
                    <td>{{ $task->user_email }}</td>
                    <td class="font-semibold">{{ $task->automation->name ?? '—' }}</td>
                    <td><span class="badge badge-purple">{{ $task->automation?->getActionLabel() ?? '—' }}</span></td>
                    <td>
                        <span class="{{ $task->execute_at->isPast() ? 'text-danger' : 'text-primary' }} font-semibold">
                            {{ $task->execute_at->diffForHumans() }}
                        </span>
                        <span class="text-xs text-muted d-block">{{ $task->execute_at->format('d/m H:i') }}</span>
                    </td>
                    <td>
                        @if ($task->execute_at->isPast())
                            <span class="badge badge-red"><i class="bi bi-exclamation-triangle me-1"></i>Vencida</span>
                        @else
                            <span class="badge badge-yellow">Pendente</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-check2-circle" style="font-size:2rem; opacity:.4;"></i>
        <p class="mt-2">Nenhuma tarefa agendada pendente.</p>
    </div>
    @endif

    <div class="card-body py-3" style="border-top: 1px solid var(--border-soft); background: var(--surface-2); border-radius: 0 0 12px 12px;">
        <p class="text-xs text-muted mb-2"><strong><i class="bi bi-info-circle me-1"></i>Configuração do Cron em Produção:</strong></p>
        <code class="text-xs" style="display:block; background:var(--surface-3,#1a1a2e); color:#a8ff78; padding:8px 12px; border-radius:8px; font-family:monospace;">
            * * * * * php {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1
        </code>
        <p class="text-xs text-muted mt-2">Adicione esta linha ao crontab do servidor para executar tarefas agendadas automaticamente a cada minuto.</p>
    </div>
</div>


{{-- Create modal --}}
{{-- Create modal --}}
<div x-data="{ open: false, actionType: 'grant_access', selectedProduct: '' }" 
     @open-create-modal.window="open = true; selectedProduct = $event.detail.productId; actionType = 'grant_access';">
    <div class="modal-overlay" x-show="open" x-cloak @click.self="open=false">
        <div class="modal">
            <div class="modal-header">
                <h3 class="card-title">Nova Automação</h3>
                <button @click="open=false" class="btn btn-ghost btn-sm"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.automations.store') }}">
                @csrf
                <div class="modal-body flex flex-col gap-6">
                    <div class="form-group">
                        <label class="form-label">Nome da automação *</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Liberar acesso Hotmart" required>
                    </div>

                    <div class="grid-2" style="gap: 20px;">
                        <div class="form-group">
                            <label class="form-label">Trigger (Quando?) *</label>
                            <select name="trigger" class="form-control" required>
                                <optgroup label="Vendas">
                                    <option value="purchase_approved">Compra Aprovada</option>
                                    <option value="purchase_refunded">Reembolso</option>
                                    <option value="chargeback">Chargeback</option>
                                </optgroup>
                                <optgroup label="Pendências / Recuperação">
                                    <option value="billet_printed">Boleto Gerado</option>
                                    <option value="pix_generated">Pix Gerado</option>
                                    <option value="cart_abandonment">Abandono de Carrinho</option>
                                    <option value="purchase_refused">Compra Recusada</option>
                                </optgroup>
                                <optgroup label="Assinatura">
                                    <option value="purchase_cancelled">Assinatura Cancelada</option>
                                    <option value="purchase_expired">Assinatura Expirada</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fonte *</label>
                            <select name="source" class="form-control" required>
                                <option value="any">Qualquer fonte</option>
                                <optgroup label="Webhooks">
                                    @foreach($webhooks as $webhook)
                                        <option value="custom_{{ $webhook->uuid }}">{{ $webhook->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Ação (O quê?) *</label>
                            <select name="action" x-model="actionType" class="form-control" required>
                                <option value="grant_access">Liberar Acesso (e Usuário)</option>
                                <option value="revoke_access">Revogar Acesso</option>
                                <option value="send_email">Enviar E-mail</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Atraso (Delay) <span class="text-faint">(segundos)</span></label>
                            <input type="number" name="delay_seconds" value="0" min="0" class="form-control" placeholder="0 = Imediato">
                            <span class="form-hint">Tempo de espera antes de executar esta ação.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Produto da plataforma</label>
                            <select name="product_id" x-model="selectedProduct" class="form-control">
                                <option value="">— Nenhum / Global —</option>
                                @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ID do produto no checkout <span class="text-faint">(opcional, para filtrar)</span></label>
                        <input type="text" name="source_product_id" class="form-control" placeholder="Deixe vazio para capturar qualquer produto">
                        <span class="form-hint">Se preenchido, a automação será disparada somente para este produto específico do checkout</span>
                    </div>

                    <div x-show="actionType === 'send_email'" x-cloak class="mt-2 p-4" style="background: var(--surface-2); border-radius: 12px; border: 1px solid var(--border-soft);">
                        <h4 class="font-semibold mb-4 text-sm"><i class="bi bi-envelope-fill me-1 text-primary"></i> Configuração de E-mail</h4>
                        
                        <div class="form-group mb-4">
                            <label class="form-label text-xs">Modelo de E-mail</label>
                            <select name="action_config[template_id]" class="form-control">
                                <option value="">-- Usar conteúdo personalizado abaixo --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->getTriggerLabel() }} - {{ $template->subject }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label text-xs">Assunto Personalizado (Opcional)</label>
                            <input type="text" name="action_config[subject]" class="form-control" placeholder="Sobrepõe o assunto do template">
                        </div>

                        <div class="form-group">
                            <label class="form-label text-xs">Corpo do E-mail (Opcional)</label>
                            <textarea name="action_config[body]" class="form-control" rows="4" placeholder="Sobrepõe o corpo do template. Use @{{buyer_name}}, @{{pix_code}}, etc."></textarea>
                        </div>
                    </div>

                    <label class="form-check pt-2">
                        <input type="checkbox" name="is_active" value="1" checked> Ativar imediatamente
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="open=false" class="btn btn-secondary">Cancelar</button>
                    <button class="btn btn-primary">Criar Automação</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
