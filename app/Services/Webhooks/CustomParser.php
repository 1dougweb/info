<?php

namespace App\Services\Webhooks;

use App\Models\CustomWebhook;
use Illuminate\Support\Facades\Log;

class CustomParser
{
    /**
     * Known field paths for major Brazilian infoproduct platforms.
     * The parser tries the explicit admin mapping first, then these IN ORDER as fallback.
     */
    private const PLATFORM_FINGERPRINTS = [
        'buyer_email' => [
            'data.customer.email',      // Cakto
            'data.buyer.email',         // Hotmart
            'buyer.email',              // Kirvano
            'customer.email',           // Eduzz / Kiwify
            'data.email',
            'email',
            'purchaser.email',          // Monetizze
        ],
        'buyer_name' => [
            'data.customer.name',       // Cakto
            'data.buyer.name',          // Hotmart
            'buyer.name',               // Kirvano
            'customer.name',            // Eduzz / Kiwify
            'data.name',
            'name',
            'purchaser.name',           // Monetizze
        ],
        'buyer_phone' => [
            'data.customer.phone',      // Cakto
            'data.buyer.phone',         // Hotmart
            'buyer.phone',
            'customer.phone',
            'data.phone',
            'phone',
        ],
        'product_name' => [
            'data.product.name',        // Cakto / Hotmart
            'product.name',             // Kirvano / Eduzz
            'data.offer.name',          // Cakto offer
            'offer.name',
            'product_name',
        ],
        'product_id' => [
            'data.product.id',          // Cakto / Hotmart
            'data.product.short_id',    // Cakto short_id
            'product.id',
            'product_id',
        ],
        'amount' => [
            'data.subscription.amount', // Cakto subscription
            'data.offer.price',         // Cakto offer price
            'data.amount',
            'amount',
            'price',
            'data.price',
            'transaction.amount',       // Hotmart
        ],
        'event' => [
            'event',                    // Cakto / Kirvano
            'type',                     // Hotmart / Kiwify
            'status',
            'data.status',
            'data.event',
        ],
        'transaction_id' => [
            'data.id',                  // Cakto purchase ID
            'data.refId',               // Cakto refId
            'transaction',              // Hotmart
            'transaction_id',
            'data.transaction_id',
            'id',
        ],
        'pix_code' => [
            'data.pix.code',
            'data.pix_code',
            'pix_code',
            'data.payment.pix_code',
        ],
        'billet_url' => [
            'data.billet.url',
            'data.boleto.url',
            'billet_url',
            'boleto_url',
        ],
    ];

    public static function parse(array $payload, string $uuid): array
    {
        $customHook = CustomWebhook::where('uuid', $uuid)->first();

        if (!$customHook) {
            Log::warning("CustomParser — webhook UUID {$uuid} not found in database.");
            return [];
        }

        $mapping = $customHook->mapping ?? [];

        // Resolve each field: explicit mapping first, auto-detect as fallback
        $resolved = [];
        foreach (self::PLATFORM_FINGERPRINTS as $field => $autoPaths) {
            $mappedPath = $mapping[$field] ?? '';
            $value      = null;

            // 1st try: explicit mapping configured by admin
            if (!empty($mappedPath)) {
                $value = self::getValue($payload, $mappedPath);
            }

            // 2nd try: auto-detect from known platform paths
            if ($value === null || $value === '') {
                foreach ($autoPaths as $path) {
                    $candidate = self::getValue($payload, $path);
                    if ($candidate !== null && $candidate !== '') {
                        $value = $candidate;
                        break;
                    }
                }
            }

            $resolved[$field] = $value;
        }

        // Bail if we couldn't find an email at all (truly unknown payload format)
        if (empty($resolved['buyer_email'])) {
            Log::warning("CustomParser — could not auto-detect buyer_email for UUID {$uuid}. Top-level keys: " . implode(', ', array_keys($payload)));
            return [];
        }

        $rawEvent = (string) ($resolved['event'] ?? 'unknown');

        Log::info("CustomParser — resolved payload for UUID {$uuid}", [
            'buyer_email'  => $resolved['buyer_email'],
            'raw_event'    => $rawEvent,
            'auto_detected' => empty($mapping['buyer_email']),
        ]);

        return [
            'source'         => 'custom_' . $uuid,
            'event'          => self::normalizeEvent(strtolower($rawEvent)),
            'raw_event'      => $rawEvent,
            'buyer_name'     => (string) ($resolved['buyer_name']    ?? 'Desconhecido'),
            'buyer_email'    => (string)  $resolved['buyer_email'],
            'buyer_phone'    => (string) ($resolved['buyer_phone']    ?? ''),
            'product_name'   => (string) ($resolved['product_name']   ?? 'Custom Product'),
            'product_id'     => (string) ($resolved['product_id']     ?? ''),
            'amount'         => (float)  ($resolved['amount']         ?? 0),
            'currency'       => 'BRL',
            'status'         => self::normalizeStatus(strtolower($rawEvent)),
            'billet_url'     => (string) ($resolved['billet_url']     ?? ''),
            'pix_code'       => (string) ($resolved['pix_code']       ?? ''),
            'checkout_url'   => (string) (self::getValue($payload, $mapping['checkout_url'] ?? '') ?? ''),
            'transaction_id' => (string) ($resolved['transaction_id'] ?? ''),
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
            str_contains($event, 'purchase_approved') => 'purchase_approved',
            str_contains($event, 'aprovad')           => 'purchase_approved',
            str_contains($event, 'approved')          => 'purchase_approved',
            str_contains($event, 'paid')              => 'purchase_approved',
            str_contains($event, 'completed')         => 'purchase_approved',
            str_contains($event, 'abandon')           => 'cart_abandonment',
            str_contains($event, 'abandono')          => 'cart_abandonment',
            str_contains($event, 'cancel')            => 'purchase_cancelled',
            str_contains($event, 'refund')            => 'purchase_refunded',
            str_contains($event, 'reembols')          => 'purchase_refunded',
            str_contains($event, 'expir')             => 'purchase_expired',
            str_contains($event, 'chargeback')        => 'chargeback',
            str_contains($event, 'recusad')           => 'purchase_refused',
            str_contains($event, 'refused')           => 'purchase_refused',
            str_contains($event, 'reject')            => 'purchase_refused',
            str_contains($event, 'billet')            => 'billet_printed',
            str_contains($event, 'boleto')            => 'billet_printed',
            str_contains($event, 'pix_gerado')        => 'pix_generated',
            str_contains($event, 'pix_generated')     => 'pix_generated',
            str_contains($event, 'pix')               => 'pix_generated',
            str_contains($event, 'waiting')           => 'waiting_payment',
            str_contains($event, 'aguardando')        => 'waiting_payment',
            default                                   => 'unknown',
        };
    }

    private static function normalizeStatus(string $event): string
    {
        return match(true) {
            str_contains($event, 'purchase_approved') => 'approved',
            str_contains($event, 'aprovad')           => 'approved',
            str_contains($event, 'approved')          => 'approved',
            str_contains($event, 'paid')              => 'approved',
            str_contains($event, 'completed')         => 'approved',
            str_contains($event, 'cancel')            => 'cancelled',
            str_contains($event, 'refund')            => 'refunded',
            str_contains($event, 'reembols')          => 'refunded',
            str_contains($event, 'expir')             => 'expired',
            str_contains($event, 'chargeback')        => 'chargeback',
            str_contains($event, 'recusa')            => 'refused',
            str_contains($event, 'refused')           => 'refused',
            str_contains($event, 'reject')            => 'refused',
            str_contains($event, 'billet')            => 'waiting_payment',
            str_contains($event, 'boleto')            => 'waiting_payment',
            str_contains($event, 'pix')               => 'waiting_payment',
            str_contains($event, 'waiting')           => 'waiting_payment',
            str_contains($event, 'aguardando')        => 'waiting_payment',
            default                                   => 'unknown',
        };
    }
}
