<?php

namespace Tests\Unit\Pos;

use App\Services\Pos\Payment\PlutoPaySignature;
use PHPUnit\Framework\TestCase;

/**
 * Signature verification is the security boundary for inbound webhooks, so it
 * is tested first and in isolation: valid, tampered, and expired signatures.
 */
class PlutoPaySignatureTest extends TestCase
{
    private string $secret = 'whsec_test_example_secret';

    private function sign(string $body, int $timestamp): string
    {
        $sig = hash_hmac('sha256', "{$timestamp}.{$body}", $this->secret);

        return "t={$timestamp},v1={$sig}";
    }

    public function test_valid_signature_is_accepted(): void
    {
        $body = '{"id":"evt_1","type":"payment.succeeded"}';
        $header = $this->sign($body, time());

        $this->assertTrue(PlutoPaySignature::verify($body, $header, $this->secret));
    }

    public function test_tampered_body_is_rejected(): void
    {
        $body = '{"id":"evt_1","type":"payment.succeeded"}';
        $header = $this->sign($body, time());

        // Body changed after signing => HMAC no longer matches.
        $this->assertFalse(PlutoPaySignature::verify($body . 'x', $header, $this->secret));
    }

    public function test_wrong_secret_is_rejected(): void
    {
        $body = '{"id":"evt_1"}';
        $header = $this->sign($body, time());

        $this->assertFalse(PlutoPaySignature::verify($body, $header, 'whsec_wrong'));
    }

    public function test_expired_timestamp_is_rejected(): void
    {
        $body = '{"id":"evt_1"}';
        // 301s in the past — outside the 300s replay window.
        $header = $this->sign($body, time() - 301);

        $this->assertFalse(PlutoPaySignature::verify($body, $header, $this->secret));
    }

    public function test_malformed_header_is_rejected(): void
    {
        $body = '{"id":"evt_1"}';

        $this->assertFalse(PlutoPaySignature::verify($body, 'garbage', $this->secret));
        $this->assertFalse(PlutoPaySignature::verify($body, '', $this->secret));
    }
}
