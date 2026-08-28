<?php

namespace App\Services\NexoPos\Payment;

use App\Services\NexoPos\Payment\Exceptions\PlutoPayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * PlutoPay Terminal HTTP client for the Flutter POS mobile app.
 * Deliberately isolated from App\Services\Pos\Payment\PlutoPayClient (Web POS).
 *
 * Docs: docs.plutopayus.com  (verified against the onboarding guide).
 *   Base URL: https://plutopayus.com/api
 *   Auth:     Authorization: Bearer sk_test_...
 *   POST /v1/terminal/create-payment   → { id, payment_intent_id, reference, ... }
 *   POST /v1/terminal/process-payment  → { status, reader_id, action_type }
 *   POST /v1/terminal/simulate-payment → { status }  (test-mode only)
 *   GET  /v1/terminal/readers          → { data: [ { id, processor_terminal_id, ... } ] }
 *
 * KEY-SHAPE GUARD: constructor throws unless PLUTOPAY_SECRET_KEY starts with
 * `sk_test_` OR `sk_live_`. Any other value is treated as unconfigured.
 * (You can constrain this further with NEXO_POS_PLUTOPAY_ALLOW_LIVE=false.)
 */
class PlutoPayClient
{
    private string $baseUrl;
    private string $secretKey;
    private string $terminalId;
    private string $readerId;
    private string $currency;
    private string $retrievePath;

    public function __construct()
    {
        $cfg = config('nexo_pos.plutopay');
        $secret = (string) ($cfg['secret_key'] ?? '');

        $isTest = str_starts_with($secret, 'sk_test_');
        $isLive = str_starts_with($secret, 'sk_live_');

        if (!$isTest && !$isLive) {
            throw new PlutoPayException(
                'Nexo POS PlutoPay is not configured: NEXO_POS_PLUTOPAY_SECRET_KEY must start with "sk_test_" or "sk_live_".'
            );
        }

        if ($isLive && !($cfg['allow_live'] ?? true)) {
            throw new PlutoPayException(
                'A live PlutoPay key was provided but NEXO_POS_PLUTOPAY_ALLOW_LIVE=false. Refusing to send real payments.'
            );
        }

        $this->secretKey  = $secret;
        $this->baseUrl    = rtrim((string) ($cfg['base_url'] ?? 'https://plutopayus.com/api'), '/');
        $this->terminalId = (string) ($cfg['terminal_id'] ?? '');
        $this->readerId   = (string) ($cfg['reader_id'] ?? '');
        $this->currency     = (string) ($cfg['currency'] ?? 'usd');
        $this->retrievePath = (string) ($cfg['retrieve_path'] ?? 'v1/transactions/{id}');
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->secretKey)
            ->acceptJson()
            ->asJson()
            ->timeout(30);
    }

    /**
     * Create a payment intent (amount in integer cents).
     *
     * @return array{provider_id:string,payment_intent_id:string,reference:?string,amount:int,currency:string,status:string}
     */
    public function createPayment(int $amountCents, string $idempotencyKey, array $metadata = []): array
    {
        $data = $this->unwrap(
            $this->http()
                ->withHeaders(['Idempotency-Key' => $idempotencyKey])
                ->post('v1/terminal/create-payment', [
                    'amount'      => $amountCents,
                    'currency'    => $this->currency,
                    'terminal_id' => $this->terminalId,
                    'metadata'    => $metadata,
                ])
        );

        return [
            'provider_id'       => (string) ($data['id'] ?? ''),
            'payment_intent_id' => (string) ($data['payment_intent_id'] ?? ($data['id'] ?? '')),
            'reference'         => $data['reference'] ?? null,
            'amount'            => (int) ($data['amount'] ?? $amountCents),
            'currency'          => (string) ($data['currency'] ?? $this->currency),
            'status'            => (string) ($data['status'] ?? 'pending'),
        ];
    }

    /**
     * Send the payment to a physical/simulated reader. When $readerIdOverride
     * is null we fall back to the reader configured in .env (single-tablet
     * setups); the mobile app usually sends its own picked reader.
     */
    public function processPayment(string $paymentIntentId, ?string $readerIdOverride = null): array
    {
        $data = $this->unwrap($this->http()->post('v1/terminal/process-payment', [
            'payment_intent_id' => $paymentIntentId,
            'reader_id'         => $readerIdOverride ?: $this->readerId,
        ]));

        return [
            'status'      => (string) ($data['status'] ?? ''),
            'reader_id'   => (string) ($data['reader_id'] ?? ''),
            'action_type' => (string) ($data['action_type'] ?? ''),
        ];
    }

    /**
     * Simulate a card tap on the sandbox reader. TEST MODE ONLY — live keys
     * return 400 by design. Accepts a per-request reader override.
     */
    public function simulatePayment(?string $readerIdOverride = null): array
    {
        $data = $this->unwrap($this->http()->post('v1/terminal/simulate-payment', [
            'reader_id' => $readerIdOverride ?: $this->readerId,
        ]));

        return ['status' => (string) ($data['status'] ?? '')];
    }

    /**
     * Ask the provider what actually happened to a payment.
     *
     * This is the escape hatch when a webhook never lands — during a host
     * outage, say. Without it a sale the customer really paid stays stuck at
     * `processing` forever and the tablet spins.
     *
     * Returns null when the id is unknown to the provider (a 404), so the
     * caller can tell "not found" apart from "request failed".
     *
     * @return array{id:string,status:string,reference:?string,amount:?int,tip_amount:?int}|null
     */
    public function retrievePayment(string $id): ?array
    {
        $path = str_replace('{id}', rawurlencode($id), $this->retrievePath);
        $res  = $this->http()->get($path);

        if ($res->status() === 404) {
            return null;
        }

        $data = $this->unwrap($res);

        return [
            'id'         => (string) ($data['id'] ?? $id),
            'status'     => (string) ($data['status'] ?? ''),
            'reference'  => $data['reference'] ?? null,
            'amount'     => isset($data['amount']) ? (int) $data['amount'] : null,
            'tip_amount' => isset($data['tip_amount']) ? (int) $data['tip_amount'] : null,
        ];
    }

    /** GET /v1/terminal/readers */
    public function listReaders(): array
    {
        $data = $this->unwrap($this->http()->get('v1/terminal/readers'));

        return is_array($data['data'] ?? null) ? $data['data'] : $data;
    }

    private function unwrap(\Illuminate\Http\Client\Response $response): array
    {
        if ($response->failed()) {
            $err = $response->json('error') ?? [];
            throw PlutoPayException::fromResponse(
                $err['message'] ?? ('PlutoPay request failed (HTTP ' . $response->status() . ').'),
                $err['code'] ?? null
            );
        }

        return (array) ($response->json('data') ?? []);
    }
}
