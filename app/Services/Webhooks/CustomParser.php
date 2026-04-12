<?php

namespace App\Services\Webhooks;

use App\Models\CustomWebhook;

class CustomParser
{
    public static function parse(array $payload, string $uuid): array
    {
        $customHook = CustomWebhook::where('uuid', $uuid)->first();
        if (!$customHook || !$customHook->mapping) {
            return [];
        }

        $mapping = $customHook->mapping;

        $rawEvent = self::getValue($payload, $mapping['event'] ?? 'unknown_key') ?? 'unknown';

        return [
            'source'         => 'custom_' . $uuid,
            'event'          => self::normalizeEvent(strtolower((string) $rawEvent)),
            'raw_event'      => $rawEvent,
            'buyer_name'     => self::getValue($payload, $mapping['buyer_name'] ?? '') ?? 'Desconhecido',
            'buyer_email'    => self::getValue($payload, $mapping['buyer_email'] ?? '') ?? '',
            'buyer_phone'    => (string) (self::getValue($payload, $mapping['buyer_phone'] ?? '') ?? ''),
            'product_name'   => (string) (self::getValue($payload, $mapping['product_name'] ?? '') ?? 'Custom Product'),
            'product_id'     => (string) (self::getValue($payload, $mapping['product_id'] ?? '') ?? ''),
            'amount'         => (float)  (self::getValue($payload, $mapping['amount'] ?? '') ?? 0),
            'currency'       => 'BRL',
            'status'         => self::normalizeStatus(strtolower((string) $rawEvent)),
            'billet_url'     => (string) (self::getValue($payload, $mapping['billet_url'] ?? '') ?? ''),
            'pix_code'       => (string) (self::getValue($payload, $mapping['pix_code'] ?? '') ?? ''),
            'checkout_url'   => (string) (self::getValue($payload, $mapping['checkout_url'] ?? '') ?? ''),
            'transaction_id' => (string) (self::getValue($payload, $mapping['transaction_id'] ?? '') ?? ''),
        ];
    }

    /**
     * Resolve a dot-notation key from a nested array.
     */
    private static function getValue(array $array, string $key): mixed
    {
        if (empty($key)) {
            return null;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return null;
            }
        }

        return $array;
    }

    private static function normalizeEvent(string $event): string
    {
        return match(true) {
            str_contains($event, 'aprovad')    => 'purchase_approved',
            str_contains($event, 'approved')   => 'purchase_approved',
            str_contains($event, 'paid')       => 'purchase_approved',
            str_contains($event, 'abandon')    => 'cart_abandonment',
            str_contains($event, 'abandono')   => 'cart_abandonment',
            str_contains($event, 'cancel')     => 'purchase_cancelled',
            str_contains($event, 'refund')     => 'purchase_refunded',
            str_contains($event, 'reembols')   => 'purchase_refunded',
            str_contains($event, 'expir')      => 'purchase_expired',
            str_contains($event, 'chargeback') => 'chargeback',
            str_contains($event, 'recusad')    => 'purchase_refused',
            str_contains($event, 'refused')    => 'purchase_refused',
            str_contains($event, 'reject')     => 'purchase_refused',
            str_contains($event, 'billet')     => 'billet_printed',
            str_contains($event, 'boleto')     => 'billet_printed',
            str_contains($event, 'pix')        => 'pix_generated',
            str_contains($event, 'waiting')    => 'waiting_payment',
            str_contains($event, 'aguardando') => 'waiting_payment',
            default                            => 'unknown',
        };
    }

    private static function normalizeStatus(string $event): string
    {
        return match(true) {
            str_contains($event, 'aprovad')    => 'approved',
            str_contains($event, 'approved')   => 'approved',
            str_contains($event, 'paid')       => 'approved',
            str_contains($event, 'cancel')     => 'cancelled',
            str_contains($event, 'refund')     => 'refunded',
            str_contains($event, 'reembols')   => 'refunded',
            str_contains($event, 'expir')      => 'expired',
            str_contains($event, 'chargeback') => 'chargeback',
            str_contains($event, 'recusa')     => 'refused',
            str_contains($event, 'refused')    => 'refused',
            str_contains($event, 'reject')     => 'refused',
            str_contains($event, 'billet')     => 'waiting_payment',
            str_contains($event, 'boleto')     => 'waiting_payment',
            str_contains($event, 'pix')        => 'waiting_payment',
            str_contains($event, 'waiting')    => 'waiting_payment',
            str_contains($event, 'aguardando') => 'waiting_payment',
            default                            => 'unknown',
        };
    }
}
