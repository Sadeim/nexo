<?php

namespace App\Console\Commands;

use App\Mail\PosReceiptMail;
use App\Models\PosOrder;
use App\Services\NexoPos\Payment\Exceptions\PlutoPayException;
use App\Services\NexoPos\Payment\PlutoPayClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Safety net for card sales whose webhook never arrived.
 *
 * Normally PlutoPay calls /webhooks/nexo-pos-plutopay and that settles the
 * order. If the site is unreachable when it fires — a host outage, a bad
 * signing secret, an endpoint the provider disabled after repeated failures —
 * the order stays at `processing` forever and the tablet spins on a payment
 * the customer really made.
 *
 * This asks the provider directly what happened and settles the row the same
 * way the webhook would, so the money and the books line up without anyone
 * touching the database by hand.
 *
 * Runs every minute from the scheduler; also safe to run by hand.
 */
class ReconcileNexoPosCards extends Command
{
    protected $signature = 'nexo-pos:reconcile-cards
        {--minutes=2 : Only touch orders left pending for at least this long}
        {--limit=50 : Max orders per run — PlutoPay allows 100 requests/minute and live sales share that budget}
        {--dry-run : Report what would change without writing}';

    protected $description = 'Settle card orders whose PlutoPay webhook never arrived.';

    /** Provider statuses that mean the money moved. */
    private const PAID = ['succeeded', 'completed', 'paid', 'captured'];

    /** Provider statuses that mean it definitively did not. */
    private const DEAD = ['failed', 'canceled', 'cancelled', 'expired', 'declined'];

    public function handle(): int
    {
        $dryRun  = (bool) $this->option('dry-run');
        $minutes = max(0, (int) $this->option('minutes'));
        $limit   = max(1, (int) $this->option('limit'));

        $stuck = PosOrder::query()
            ->where('payment_method', 'card')
            ->whereIn('status', ['awaiting_payment', 'processing'])
            ->where('updated_at', '<=', now()->subMinutes($minutes))
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($stuck->isEmpty()) {
            $this->info('No pending card orders to reconcile.');
            return self::SUCCESS;
        }

        $this->line("Checking {$stuck->count()} pending card order(s) with PlutoPay (max {$limit} per run)...");

        $settled = $failed = $unknown = 0;

        foreach ($stuck as $order) {
            // The transactions endpoint keys off the provider's own UUID —
            // data.id, which create-payment hands back as provider_payment_id.
            // payment_intent_id is a pi_… value and reference is a txn_… value;
            // PlutoPay answers both with a 500 rather than a 404 (their bug,
            // confirmed by their support), so sending either would turn a
            // harmless "unknown id" into a hard error. Only the UUID is safe.
            $uuid = (string) $order->provider_payment_id;

            if ($uuid === '') {
                $this->warn("#{$order->order_number}: no provider UUID recorded, cannot look it up. Leaving alone.");
                $unknown++;
                continue;
            }

            try {
                $remote = $this->pluto()->retrievePayment($uuid);
            } catch (PlutoPayException $e) {
                Log::warning('NexoPos reconcile lookup failed', [
                    'order_id' => $order->id,
                    'id'       => $uuid,
                    'error'    => $e->getMessage(),
                ]);
                $this->warn("#{$order->order_number}: lookup failed ({$e->getMessage()})");
                $unknown++;
                continue;
            }

            if ($remote === null) {
                // A clean 404 — the provider has never heard of it.
                $this->line("#{$order->order_number}: unknown to the provider, leaving pending.");
                $unknown++;
                continue;
            }

            $status = strtolower($remote['status'] ?? '');

            if (in_array($status, self::PAID, true)) {
                $this->info("#{$order->order_number}: provider says {$status} → settling.");
                if (!$dryRun) {
                    $this->settle($order, $remote);
                }
                $settled++;
            } elseif (in_array($status, self::DEAD, true)) {
                $this->warn("#{$order->order_number}: provider says {$status} → marking failed.");
                if (!$dryRun) {
                    $order->forceFill([
                        'status'         => 'failed',
                        'failure_reason' => $status,
                    ])->save();
                }
                $failed++;
            } else {
                // Still genuinely in flight — the customer may not have tapped yet.
                $this->line("#{$order->order_number}: still '{$status}', leaving pending.");
                $unknown++;
            }
        }

        $this->newLine();
        $this->info("Settled: {$settled} · Failed: {$failed} · Left pending: {$unknown}");

        if ($dryRun) {
            $this->comment('Dry run — nothing written.');
        }

        return self::SUCCESS;
    }

    /**
     * Apply the same settlement the webhook would, including the house rule
     * that a card tip pays the employee in whole dollars and the cents join
     * the shop's fees.
     */
    private function settle(PosOrder $order, array $remote): void
    {
        DB::transaction(function () use ($order, $remote) {
            /** @var PosOrder $fresh */
            $fresh = PosOrder::whereKey($order->id)->lockForUpdate()->first();

            if ($fresh->isTerminal()) {
                return; // the webhook beat us to it
            }

            $tipCents = (int) ($remote['tip_amount'] ?? 0);

            if ($tipCents > 0) {
                $employeeTipCents = intdiv($tipCents, 100) * 100;
                $remainderCents   = $tipCents - $employeeTipCents;
            } else {
                $employeeTipCents = (int) round((float) $fresh->tip * 100);
                $remainderCents   = (int) round((float) $fresh->tip_remainder * 100);
            }

            $newTip       = round($employeeTipCents / 100, 2);
            $newRemainder = round($remainderCents / 100, 2);

            $fresh->update([
                'status'        => 'completed',
                'reference'     => $remote['reference'] ?? $fresh->reference,
                'tip'           => $newTip,
                'tip_remainder' => $newRemainder,
                'total'         => round(
                    (float) $fresh->subtotal + (float) $fresh->card_fee + $newTip + $newRemainder,
                    2
                ),
            ]);

            if (!empty($fresh->customer_email) && !$fresh->receipt_sent_at) {
                try {
                    Mail::to($fresh->customer_email)
                        ->send(new PosReceiptMail($fresh->fresh(['items', 'employee'])));
                    $fresh->forceFill(['receipt_sent_at' => now()])->save();
                } catch (\Throwable $e) {
                    Log::warning('NexoPos reconcile receipt failed', [
                        'order_id' => $fresh->id,
                        'err'      => $e->getMessage(),
                    ]);
                }
            }
        });
    }

    private function pluto(): PlutoPayClient
    {
        return app(PlutoPayClient::class);
    }
}
