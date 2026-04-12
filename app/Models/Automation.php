<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Automation extends Model
{
    protected $fillable = [
        'name', 'trigger', 'source', 'source_product_id', 'product_id',
        'action', 'action_config', 'is_active', 'delay_seconds', 'conditions',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'action_config' => 'array',
        'conditions'    => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTriggerLabel(): string
    {
        return match($this->trigger) {
            'purchase_approved'   => 'Compra Aprovada',
            'purchase_cancelled'  => 'Compra Cancelada',
            'purchase_refused'    => 'Compra Recusada',
            'purchase_refunded'   => 'Reembolso',
            'purchase_expired'    => 'Assinatura Expirada',
            'chargeback'          => 'Chargeback',
            'billet_printed'      => 'Boleto Gerado',
            'pix_generated'       => 'Pix Gerado',
            'waiting_payment'     => 'Aguardando Pagamento',
            'cart_abandonment'    => 'Abandono de Carrinho',
            default               => ucfirst($this->trigger),
        };
    }

    public function getActionLabel(): string
    {
        return match($this->action) {
            'grant_access'  => 'Liberar Acesso',
            'revoke_access' => 'Revogar Acesso',
            'send_email'    => 'Enviar E-mail',
            'create_user'   => 'Criar Usuário',
            default         => ucfirst($this->action),
        };
    }
}
