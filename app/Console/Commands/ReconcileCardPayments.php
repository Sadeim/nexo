<?php

namespace App\Console\Commands;

use App\Models\PosTransaction;
use App\Services\Pos\Payment\Exceptions\PlutoPayException;
use App\Services\Pos\Payment\PlutoPayClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Safety net for lost/late webhooks.
 *
 * For any card transaction that has been stuck in awaiting_payment/processing
 * past a short grace period, ask PlutoPay for the authoritative status and
 * reconcile our row. Without this a payment that SUCCEEDED at PlutoPay but
 * whose webhook never arrived would sit unsettled — and could be wrongly read
 * as a failure (lost money). This is the ONLY backstop for that case.
 *
 * 🔴 Only PlutoPay's own status settles a sale — we never invent one.
 */
class ReconcileCardPayments extends Command
{
    protected $signature = 'pos:reconcile-card-payments {--minutes=2 : Only touch rows older than this}';

    protected $description = 'Reconcile stuck PlutoPay card transactions against the gateway.';

    public function handle(PlutoPayClient $pluto): int
    {
        $graceMinutes = (int) $this->option('minutes');

        $stuck = PosTransaction::where('payment_method', 'card')
            ->whereIn('status', ['awaiting_payment', 'processing'])
            ->whereNotNull('payment_intent_id')
            ->where('created_at', '<=', now()->subMinutes($graceMinutes))
            ->get();

        if ($stuck->isEmpty()) {
            $this->info('No stuck card transactions to reconcile.');
            return self::SUCCESS;
        }

        $reconciled = 0;

        foreach ($stuck as $txn) {
            try {
                $remote = $pluto->retrievePayment($txn->payment_intent_id);
            } catch (PlutoPayException $e) {
                Log::warning('Reconcile: retrievePayment failed', [
                    'transaction_id' => $txn->id,
                    'error'          => $e->getMessage(),
                ]);
                continue;
            }

            $newStatus = $this->mapStatus($remote['status']);
            if ($newStatus === null || $newStatus === $txn->status) {
                continue; // still pending at the gateway, or unchanged.
            }

            $updates = ['status' => $newStatus];
            if ($newStatus === 'completed') {
                $updates['reference'] = $remote['reference'] ?? $txn->reference;
            } elseif ($newStatus === 'failed' && !$txn->failure_reason) {
                $updates['failure_reason'] = 'reconciled:' . $remote['status'];
            }

            $txn->update($updates);
            $reconciled++;

            Log::info('Reconciled card transaction', [
                'transaction_id' => $txn->id,
                'remote_status'  => $remote['status'],
                'new_status'     => $newStatus,
            ]);
        }

        $this->info("Reconciled {$reconciled} of {$stuck->count()} stuck transaction(s).");
        return self::SUCCESS;
    }

    /** Map a PlutoPay payment status to our transaction status. */
    private function mapStatus(string $remote): ?string
    {
        return match (strtolower($remote)) {
            'succeeded', 'paid', 'completed'    => 'completed',
            'failed', 'declined'                => 'failed',
            'canceled', 'cancelled'             => 'canceled',
            default                             => null, // pending/processing -> leave as-is
        };
    }
}
