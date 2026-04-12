<?php

namespace App\Services\Webhooks;

class HotmartParser
{
    public static function parse(array $payload): array
    {
        $data = $payload['data'] ?? $payload;
        $buyer = $data['buyer'] ?? [];
        $purchase = $data['purchase'] ?? [];
        $product = $data['product'] ?? [];

        $eventType = $payload['event'] ?? '';

        return [
            'source'         => 'hotmart',
            'event'          => self::normalizeEvent($eventType),
            'raw_event'      => $eventType,
            'buyer_name'     => $buyer['name'] ?? '',
            'buyer_email'    => $buyer['email'] ?? '',
            'buyer_phone'    => $buyer['checkout_phone'] ?? '',
            'product_name'   => $product['name'] ?? '',
            'product_id'     => (string) ($product['id'] ?? ''),
            'transaction_id' => $purchase['transaction'] ?? '',
            'amount'         => ($purchase['price']['value'] ?? 0) / 100,
            'currency'       => $purchase['price']['currency_value'] ?? 'BRL',
            'status'         => self::normalizeStatus($eventType),
        ];
    }

    private static function normalizeEvent(string $event): string
    {
        return match(true) {
            str_contains($event, 'APPROVED')   => 'purchase_approved',
            str_contains($event, 'CANCELLED')  => 'purchase_cancelled',
            str_contains($event, 'REFUNDED')   => 'purchase_refunded',
            str_contains($event, 'EXPIRED')    => 'purchase_expired',
            str_contains($event, 'CHARGEBACK') => 'chargeback',
            str_contains($event, 'REFUSED')    => 'purchase_refused',
            str_contains($event, 'BILLET')     => 'billet_printed',
            str_contains($event, 'PIX')        => 'pix_generated',
            str_contains($event, 'WAITING')    => 'waiting_payment',
            default                            => 'unknown',
        };
    }

    private static function normalizeStatus(string $event): string
    {
        return match(true) {
            str_contains($event, 'APPROVED')   => 'approved',
            str_contains($event, 'CANCELLED')  => 'cancelled',
            str_contains($event, 'REFUNDED')   => 'refunded',
            str_contains($event, 'EXPIRED')    => 'expired',
            str_contains($event, 'CHARGEBACK') => 'chargeback',
            str_contains($event, 'REFUSED')    => 'refused',
            str_contains($event, 'BILLET')     => 'waiting_payment',
            str_contains($event, 'PIX')        => 'waiting_payment',
            str_contains($event, 'WAITING')    => 'waiting_payment',
            default                            => 'unknown',
        };
    }
}
