<?php

namespace App\Services\Webhooks;

class CaktoParser
{
    public static function parse(array $payload): array
    {
        $customer = $payload['customer'] ?? [];
        $product  = $payload['product'] ?? [];
        $event    = $payload['event'] ?? $payload['type'] ?? '';

        return [
            'source'         => 'cakto',
            'event'          => self::normalizeEvent($event),
            'raw_event'      => $event,
            'buyer_name'     => trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
            'buyer_email'    => $customer['email'] ?? '',
            'buyer_phone'    => $customer['phone'] ?? '',
            'product_name'   => $product['name'] ?? '',
            'product_id'     => (string) ($product['id'] ?? ''),
            'transaction_id' => $payload['id'] ?? $payload['transaction_id'] ?? '',
            'amount'         => ($payload['amount'] ?? 0) / 100,
            'currency'       => $payload['currency'] ?? 'BRL',
            'status'         => self::normalizeStatus($event),
        ];
    }

    private static function normalizeEvent(string $event): string
    {
        return match(true) {
            str_contains(strtolower($event), 'paid')        => 'purchase_approved',
            str_contains(strtolower($event), 'approved')    => 'purchase_approved',
            str_contains(strtolower($event), 'cancel')      => 'purchase_cancelled',
            str_contains(strtolower($event), 'refund')      => 'purchase_refunded',
            str_contains(strtolower($event), 'expired')     => 'purchase_expired',
            str_contains(strtolower($event), 'chargeback')  => 'chargeback',
            str_contains(strtolower($event), 'refused')     => 'purchase_refused',
            str_contains(strtolower($event), 'reject')      => 'purchase_refused',
            str_contains(strtolower($event), 'billet')      => 'billet_printed',
            str_contains(strtolower($event), 'pix')         => 'pix_generated',
            str_contains(strtolower($event), 'waiting')     => 'waiting_payment',
            default                                         => 'unknown',
        };
    }

    private static function normalizeStatus(string $event): string
    {
        return match(true) {
            str_contains(strtolower($event), 'paid')     => 'approved',
            str_contains(strtolower($event), 'approved') => 'approved',
            str_contains(strtolower($event), 'cancel')   => 'cancelled',
            str_contains(strtolower($event), 'refund')   => 'refunded',
            str_contains(strtolower($event), 'expired')  => 'expired',
            str_contains(strtolower($event), 'chargeback') => 'chargeback',
            str_contains(strtolower($event), 'refused')  => 'refused',
            str_contains(strtolower($event), 'reject')   => 'refused',
            str_contains(strtolower($event), 'billet')   => 'waiting_payment',
            str_contains(strtolower($event), 'pix')      => 'waiting_payment',
            str_contains(strtolower($event), 'waiting')  => 'waiting_payment',
            default                                      => 'unknown',
        };
    }
}
