<?php

namespace App\Services\Pos\Payment;

use App\Services\Pos\Payment\Exceptions\PlutoPayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Thin HTTP wrapper around the PlutoPay Terminal API.
 *
 * Confirmed from docs (guides/*.html, 2026-07):
 *   - Base URL:        https://plutopayus.com/api   (path /api, NOT api. subdomain)
 *   - Auth:            Authorization: Bearer sk_test_...
 *   - Idempotency:     Idempotency-Key header on POST create-payment
 *   - connection-token -> data.secret
 *   - create-payment   -> data.id, data.reference, data.client_secret,
 *                         data.amount, data.currency, data.status
 *   - process-payment  -> data.status, data.reader_id, data.action_type
 *   - simulate-payment -> data.status   (test-mode only; live key => 400)
 *
 * 🔴 TEST-MODE GUARD: the constructor refuses to operate unless the secret key
 * starts with `sk_test_`. This is a hard sentinel against accidental live use
 * in this phase.
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
        $cfg = config('services.plutopay');

        $secret = (string) ($cfg['secret_key'] ?? '');

        // 🔴 Hard TEST-MODE guard. No request may leave this process with a
        // non-test key during this phase.
        if (!str_starts_with($secret, 'sk_test_')) {
            throw new PlutoPayException(
                'PlutoPay is locked to TEST MODE: PLUTOPAY_SECRET_KEY must start with "sk_test_".'
            );
        }

        $this->secretKey    = $secret;
        $this->baseUrl      = rtrim((string) $cfg['base_url'], '/');
        $this->terminalId   = (string) ($cfg['terminal_id'] ?? '');
        $this->readerId     = (string) ($cfg['reader_id'] ?? '');
        $this->currency     = (string) ($cfg['currency'] ?? 'usd');
        $this->retrievePath = (string) ($cfg['retrieve_path'] ?? 'v1/terminal/payment/{id}');
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->secretKey)
            ->acceptJson()
            ->asJson()
            ->timeout(30);
    }

    /** POST /v1/terminal/connection-token -> data.secret */
    public function connectionToken(): string
    {
        $data = $this->unwrap($this->http()->post('v1/terminal/connection-token'));

        return (string) ($data['secret'] ?? '');
    }

    /**
     * POST /v1/terminal/create-payment. $idempotencyKey MUST be a stable value
     * per logical sale so a double-click never creates two charges.
     *
     * @return array{id:string,reference:?string,client_secret:?string,amount:int,currency:string,status:string}
     */
    public function createPayment(int $amountCents, string $idempotencyKey, array $metadata = []): array
    {
        $data = $this->unwrap(
            $this->http()
                ->withHeaders(['Idempotency-Key' => $idempotencyKey])
                ->post('v1/terminal/create-payment', [
                    'amount'      => $amountCents,     // integer cents
                    'currency'    => $this->currency,
                    'terminal_id' => $this->terminalId,
                    'metadata'    => $metadata,
                ])
        );

        return [
            'id'            => (string) ($data['id'] ?? ''),
            'reference'     => $data['reference'] ?? null,
            'client_secret' => $data['client_secret'] ?? null,
            'amount'        => (int) ($data['amount'] ?? $amountCents),
            'currency'      => (string) ($data['currency'] ?? $this->currency),
            'status'        => (string) ($data['status'] ?? 'pending'),
        ];
    }

    /** POST /v1/terminal/process-payment -> data.status */
    public function processPayment(string $paymentIntentId): array
    {
        $data = $this->unwrap($this->http()->post('v1/terminal/process-payment', [
            'payment_intent_id' => $paymentIntentId,
            'reader_id'         => $this->readerId,
        ]));

        return [
            'status'      => (string) ($data['status'] ?? ''),
            'reader_id'   => (string) ($data['reader_id'] ?? ''),
            'action_type' => (string) ($data['action_type'] ?? ''),
        ];
    }

    /**
     * POST /v1/terminal/simulate-payment (TEST MODE ONLY). Simulates a card tap
     * on the reader so the webhook fires. Live key => 400 by design.
     */
    public function simulatePayment(string $cardNumber): array
    {
        $data = $this->unwrap($this->http()->post('v1/terminal/simulate-payment', [
            'reader_id'   => $this->readerId,
            'card_number' => $cardNumber,
        ]));

        return [
            'status' => (string) ($data['status'] ?? ''),
        ];
    }

    /**
     * POST /v1/terminals — register a (simulated, in test mode) reader.
     * Returns the created device (id like tmr_... / tmr_simulated_...).
     */
    public function registerTerminal(string $name, string $registrationCode): array
    {
        $data = $this->unwrap($this->http()->post('v1/terminals', [
            'name'              => $name,
            'registration_code' => $registrationCode,
        ]));

        return [
            'id'   => (string) ($data['id'] ?? ''),
            'name' => (string) ($data['name'] ?? $name),
        ];
    }

    /** GET /v1/terminal/readers -> list of readers (each has an id). */
    public function listReaders(): array
    {
        $data = $this->unwrap($this->http()->get('v1/terminal/readers'));

        // Some APIs wrap the list under data, others under data.data — accept both.
        return is_array($data['data'] ?? null) ? $data['data'] : $data;
    }

    /** GET /v1/terminals -> list of registered terminals (each has an id). */
    public function listTerminals(): array
    {
        $data = $this->unwrap($this->http()->get('v1/terminals'));

        return is_array($data['data'] ?? null) ? $data['data'] : $data;
    }

    /**
     * GET single payment for reconciliation. Path is configurable because the
     * public docs don't spell it out; default v1/terminal/payment/{id}.
     */
    public function retrievePayment(string $paymentIntentId): array
    {
        $path = str_replace('{id}', rawurlencode($paymentIntentId), $this->retrievePath);
        $data = $this->unwrap($this->http()->get($path));

        return [
            'id'        => (string) ($data['id'] ?? $paymentIntentId),
            'status'    => (string) ($data['status'] ?? ''),
            'reference' => $data['reference'] ?? null,
        ];
    }

    /**
     * Validate an HTTP response and return its `data` envelope, or throw with
     * the PlutoPay error message/code.
     */
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
