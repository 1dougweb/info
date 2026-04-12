@extends('layouts.admin')
@section('title', 'Mapear Webhook')
@section('breadcrumb', 'Webhooks Customizados › Mapear')

@section('content')
<div class="page-header">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="page-title"><i class="bi bi-diagram-3 me-2"></i> {{ $customWebhook->name }}</h1>
            <p class="text-muted text-sm mt-2">Mapeie as variáveis recebidas para as variáveis internas da plataforma.</p>
        </div>
        <a href="{{ route('admin.custom-webhooks.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success mb-6"><i class="bi bi-check2"></i> {{ session('success') }}</div>
@endif

<div class="card mb-6">
    <div class="card-body">
        <h3 class="font-semibold mb-2">Sua URL de Webhook:</h3>
        <div class="flex gap-2" x-data="{ copied: false }">
            <input type="text" class="form-control" value="{{ route('api.webhooks.custom', $customWebhook->uuid) }}" readonly id="webhookUrl" style="background: var(--surface-2); font-family: monospace;">
            <button class="btn btn-primary" 
                    @click="navigator.clipboard.writeText(document.getElementById('webhookUrl').value); copied = true; setTimeout(() => copied = false, 2000)"
                    :class="copied ? 'btn-success' : 'btn-primary'"
                    style="min-width: 110px;">
                <span x-show="!copied"><i class="bi bi-copy"></i> Copiar</span>
                <span x-show="copied" x-cloak><i class="bi bi-check2"></i> Copiado!</span>
            </button>
        </div>
        <p class="text-xs text-muted mt-2">Cole esta URL na plataforma externa (Eduzz, Braip, PerfectPay, etc).</p>
    </div>
</div>

<div class="grid-2" style="gap: 24px; align-items: start;">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="font-semibold"><i class="bi bi-braces"></i> Último Payload Recebido</h3>
            <button class="btn btn-ghost btn-sm" onclick="window.location.reload()"><i class="bi bi-arrow-clockwise"></i> Atualizar Pág</button>
        </div>
        <div class="card-body" style="background: var(--surface-2); overflow-x: auto; font-family: monospace; font-size: 0.85rem;">
            @if ($customWebhook->last_payload)
                <pre>{{ json_encode($customWebhook->last_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <div class="text-center text-muted py-6">
                    <i class="bi bi-hourglass-split fs-3"></i>
                    <p class="mt-2">Aguardando o primeiro envio de dados...</p>
                    <p class="text-xs">Faça uma compra de teste na sua plataforma externa para recebermos as chaves.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="font-semibold"><i class="bi bi-link-45deg"></i> Mapeamento de Chaves</h3></div>
        <div class="card-body">
            @if (!$customWebhook->last_payload)
                <div class="alert alert-warning text-sm">Receba um payload primeiro para poder mapear as chaves.</div>
            @else
                <form method="POST" action="{{ route('admin.custom-webhooks.update', $customWebhook) }}">
                    @csrf @method('PUT')
                    
                    @php
                        function extractKeys($array, $prefix = '') {
                            $keys = [];
                            foreach ($array as $key => $value) {
                                $currentKey = $prefix ? $prefix . '.' . $key : $key;
                                if (is_array($value) && !wp_is_numeric_array($value)) {
                                    $keys = array_merge($keys, extractKeys($value, $currentKey));
                                } else {
                                    $keys[] = $currentKey;
                                }
                            }
                            return $keys;
                        }
                        function wp_is_numeric_array($array) {
                            foreach (array_keys($array) as $key) { if (!is_int($key)) return false; }
                            return true;
                        }
                        $availableKeys = extractKeys($customWebhook->last_payload);
                    @endphp

                    <div class="flex flex-col gap-4">
                        @foreach([
                            'event' => 'Tipo de Evento (Ex: status, event)',
                            'buyer_email' => 'E-mail do Comprador',
                            'buyer_name' => 'Nome do Comprador',
                            'product_id' => 'ID do Produto',
                            'transaction_id' => 'ID da Transação',
                            'amount' => 'Valor da Compra (se houver)'
                        ] as $field => $label)
                        <div class="form-group">
                            <label class="form-label text-sm">{{ $label }}</label>
                            <select name="mapping[{{ $field }}]" class="form-control text-sm">
                                <option value="">-- Não mapear --</option>
                                @foreach($availableKeys as $key)
                                    <option value="{{ $key }}" {{ ($customWebhook->mapping[$field] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $key }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-6 w-full">Salvar Mapeamento</button>
                    <p class="text-xs text-muted mt-4 text-center">Somente o e-mail, nome e evento são estritamente necessários para liberar cursos.</p>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
