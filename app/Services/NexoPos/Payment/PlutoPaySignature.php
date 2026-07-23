<?php

namespace App\Services\NexoPos\Payment;

/**
 * Reference-spec verification of inbound PlutoPay webhook signatures
 * (see docs.plutopayus.com — Webhooks). MUST be called on the RAW body,
 * before any json_decode.
 *
 *   header format:  t={timestamp},v1={signature}
 *   signed payload: "{timestamp}.{rawBody}"
 *   algorithm:      HMAC-SHA256, constant-time compare
 *   replay window:  300 seconds
 */
class PlutoPaySignature
{
    public static function verify(
        string $rawBody,
        string $header,
        string $secret,
        int $toleranceSeconds = 300
    ): bool {
        $parts = [];
        foreach (explode(',', $header) as $kv) {
            [$k, $v] = array_pad(explode('=', $kv, 2), 2, null);
            $parts[$k] = $v;
        }
        $timestamp = $parts['t'] ?? null;
        $provided  = $parts['v1'] ?? null;
        if (!$timestamp || !$provided) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > $toleranceSeconds) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

        return hash_equals($expected, $provided);
    }
}
