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

<div class="card mb-8">
    <div class="table-wrap" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Trigger</th>
                    <th>Fonte</th>
                    <th>Ação</th>
                    <th>Produto</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($automations as $auto)
                <tr>
                    <td class="font-semibold">{{ $auto->name }}</td>
                    <td><span class="badge badge-yellow">{{ $auto->getTriggerLabel() }}</span></td>
                    <td><span class="badge badge-blue" style="text-transform: capitalize;">{{ $auto->source === 'any' ? 'Qualquer' : $auto->source }}</span></td>
                    <td><span class="badge badge-purple">{{ $auto->getActionLabel() }}</span></td>
                    <td class="text-sm text-muted">{{ $auto->product->title ?? ($auto->source_product_id ?? '—') }}</td>
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
                @empty
                <tr><td colspan="7" class="text-center text-muted p-6">Nenhuma automação configurada</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Create modal --}}
{{-- Create modal --}}
<div x-data="{ open: false, actionType: 'grant_access' }" @open-create-modal.window="open = true">
    <div class="modal-overlay" x-show="open" x-cloak @click.self="open=false">
        <div class="modal">
            <div class="modal-header">
                <h3 class="card-title">Nova Automação</h3>
                <button @click="open=false" class="btn btn-ghost btn-sm"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('admin.automations.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nome da automação *</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Liberar acesso Hotmart" required>
                    </div>

                    <div class="grid-2" style="gap: 16px;">
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
                                <option value="hotmart">Hotmart</option>
                                <option value="cakto">Cakto</option>
                                <option value="wikify">Wikify</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Ação (O quê?) *</label>
                            <select name="action" x-model="actionType" class="form-control" required>
                                <option value="grant_access">Liberar Acesso</option>
                                <option value="revoke_access">Revogar Acesso</option>
                                <option value="send_email">Enviar E-mail</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Produto da plataforma</label>
                            <select name="product_id" class="form-control">
                                <option value="">— Nenhum —</option>
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

                    <div x-show="actionType === 'send_email'" x-cloak class="mt-4 mb-4 p-4" style="background: var(--surface-2); border-radius: 8px; border: 1px solid var(--border-soft);">
                        <h4 class="font-semibold mb-3"><i class="bi bi-envelope"></i> Configuração do E-mail</h4>
                        <div class="form-group mb-3">
                            <label class="form-label">Assunto</label>
                            <input type="text" name="action_config[subject]" class="form-control" placeholder="Ex: Seu acesso ao @{{product_name}}!">
                        </div>
                        <div class="form-group mb-2">
                            <label class="form-label">Corpo</label>
                            <textarea name="action_config[body]" class="form-control" rows="4" placeholder="Ex: Olá @{{buyer_name}}, acesso via @{{buyer_email}}"></textarea>
                        </div>
                        <div class="text-xs text-muted" style="line-height: 1.5;">
                            <strong>Variáveis disponíveis:</strong><br>
                            <code>@{{buyer_name}}</code>, <code>@{{buyer_email}}</code>, <code>@{{product_name}}</code>, <code>@{{transaction_id}}</code>, <code>@{{amount}}</code><br>
                            <em>(Em webhooks customizados, você também pode digitar qualquer chave recebida do payload original envolvendo com chaves duplas Ex: <code>@{{minha_chave}}</code>)</em>
                        </div>
                    </div>

                    <label class="form-check">
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
