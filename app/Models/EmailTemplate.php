<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['trigger', 'subject', 'body', 'is_active'];

    public function getTriggerLabel(): string
    {
        $labels = [
            'purchase_approved' => 'Compra Aprovada',
            'billet_printed'    => 'Boleto Gerado',
            'pix_generated'     => 'Pix Gerado',
            'cart_abandonment'  => 'Abandono de Carrinho',
            'purchase_refused'  => 'Compra Recusada',
            'purchase_refunded' => 'Reembolso',
            'purchase_cancelled'=> 'Assinatura Cancelada',
        ];

        return $labels[$this->trigger] ?? $this->trigger;
    }
}
