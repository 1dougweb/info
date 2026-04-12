<?php

namespace App\Services\Webhooks;

class WikifyParser
{
    public static function parse(array $payload): array
    {
        $buyer   = $payload['buyer'] ?? $payload['customer'] ?? [];
        $product = $payload['product'] ?? [];
        $event   = $payload['event'] ?? $payload['type'] ?? '';

        return [
            'source'         => 'wikify',
            'event'          => self::normalizeEvent($event),
            'raw_event'      => $event,
            'buyer_name'     => $buyer['name'] ?? trim(($buyer['first_name'] ?? '') . ' ' . ($buyer['last_name'] ?? '')),
            'buyer_email'    => $buyer['email'] ?? '',
            'buyer_phone'    => $buyer['phone'] ?? '',
            'product_name'   => $product['name'] ?? '',
            'product_id'     => (string) ($product['id'] ?? ''),
            'transaction_id' => $payload['transaction_id'] ?? $payload['order_id'] ?? '',
            'amount'         => $payload['amount'] ?? 0,
            'currency'       => $payload['currency'] ?? 'BRL',
            'status'         => self::normalizeStatus($event),
        ];
    }

    private static function normalizeEvent(string $event): string
    {
        return match(true) {
            str_contains(strtolower($event), 'compra_aprovada')  => 'purchase_approved',
            str_contains(strtolower($event), 'approved')         => 'purchase_approved',
            str_contains(strtolower($event), 'cancel')           => 'purchase_cancelled',
            str_contains(strtolower($event), 'refund')           => 'purchase_refunded',
            str_contains(strtolower($event), 'expirado')         => 'purchase_expired',
            str_contains(strtolower($event), 'expired')          => 'purchase_expired',
            str_contains(strtolower($event), 'chargeback')       => 'chargeback',
            str_contains(strtolower($event), 'refused')          => 'purchase_refused',
            str_contains(strtolower($event), 'reject')           => 'purchase_refused',
            str_contains(strtolower($event), 'billet')           => 'billet_printed',
            str_contains(strtolower($event), 'pix')              => 'pix_generated',
            str_contains(strtolower($event), 'waiting')          => 'waiting_payment',
            default                                              => 'unknown',
        };
    }

    private static function normalizeStatus(string $event): string
    {
        return match(true) {
            str_contains(strtolower($event), 'compra_aprovada')  => 'approved',
            str_contains(strtolower($event), 'approved')         => 'approved',
            str_contains(strtolower($event), 'cancel')           => 'cancelled',
            str_contains(strtolower($event), 'refund')           => 'refunded',
            str_contains(strtolower($event), 'expirado')         => 'expired',
            str_contains(strtolower($event), 'expired')          => 'expired',
            str_contains(strtolower($event), 'chargeback')       => 'chargeback',
            str_contains(strtolower($event), 'refused')          => 'refused',
            str_contains(strtolower($event), 'reject')           => 'refused',
            str_contains(strtolower($event), 'billet')           => 'waiting_payment',
            str_contains(strtolower($event), 'pix')              => 'waiting_payment',
            str_contains(strtolower($event), 'waiting')          => 'waiting_payment',
            default                                              => 'unknown',
        };
    }
}
