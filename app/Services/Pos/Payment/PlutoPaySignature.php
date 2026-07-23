<?php

namespace App\Services\Pos\Payment;

/**
 * Verifies inbound PlutoPay webhook signatures.
 *
 * The verify() body is the reference implementation from the PlutoPay docs
 * (guides/webhooks.html), kept character-for-character so behaviour matches
 * the provider exactly:
 *   - header format:  t={timestamp},v1={signature}
 *   - signed payload: "{timestamp}.{rawBody}"
 *   - algorithm:      HMAC-SHA256, constant-time compare
 *   - replay window:  300 seconds (default)
 *
 * MUST be called on the RAW request body, before any json_decode.
 */
class PlutoPaySignature
{
    /**
     * @param  string  $rawBody  The exact raw HTTP body bytes.
     * @param  string  $header   The X-PlutoPay-Signature header value.
     * @param  string  $secret   The webhook signing secret (whsec_...).
     */
    public static function verify(
        string $rawBody,
        string $header,
        string $secret,
        int $toleranceSeconds = 300
    ): bool {
        // Parse "t=...,v1=..."
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

        // Replay protection
        if (abs(time() - (int) $timestamp) > $toleranceSeconds) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

        return hash_equals($expected, $provided);
    }
}
