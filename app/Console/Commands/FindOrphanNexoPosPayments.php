<?php

namespace App\Console\Commands;

use App\Models\PosOrder;
use App\Services\NexoPos\Payment\Exceptions\PlutoPayException;
use App\Services\NexoPos\Payment\PlutoPayClient;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Find card charges the provider has that we have no order for.
 *
 * Reconciliation can only ask about payments we already started, so a charge
 * taken outside the app — from the provider's own POS screens, say, when the
 * tablet stops responding — is invisible to it no matter how often it runs.
 * That is a gap money falls through: the sale happens, the customer is
 * charged, and no employee is ever credited for the work.
 *
 * Deliberately does NOT create orders. Who performed the service is not in
 * the payment, so the rows this prints need a person to attribute them.
 *
 * Writes nothing.
 */
class FindOrphanNexoPosPayments extends Command
{
    protected $signature = 'nexo-pos:find-orphan-payments
        {--from= : Start date YYYY-MM-DD (default: 7 days ago)}
        {--to= : End date YYYY-MM-DD (default: today)}
        {--pages=10 : Max pages of 100 to pull — the provider allows 100 requests/minute}';

    protected $description = 'Read-only: list provider card payments that have no matching order.';

    public function handle(): int
    {
        $tz   = config('app.timezone');
        $from = (string) ($this->option('from') ?: Carbon::now($tz)->subDays(7)->toDateString());
        $to   = (string) ($this->option('to') ?: Carbon::now($tz)->toDateString());
        $max  = max(1, (int) $this->option('pages'));

        $this->line("Pulling provider transactions {$from} to {$to}...");

        $remote = [];
        try {
            for ($page = 1; $page <= $max; $page++) {
                $batch = (new PlutoPayClient())->listPayments($from, $to, $page);
                $remote = array_merge($remote, $batch);
                if (count($batch) < 100) {
                    break;
                }
            }
        } catch (PlutoPayException $e) {
            $this->error("Provider lookup failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        if (empty($remote)) {
            $this->warn('No transactions returned for that window.');

            return self::SUCCESS;
        }

        // Match on the ids we actually store. Metadata is not usable here:
        // the provider reports it only on terminal events, and its
        // `terminal_id` key holds two different identifier spaces depending
        // on the event shape.
        $ids   = array_values(array_filter(array_map(fn ($r) => (string) ($r['id'] ?? ''), $remote)));
        $known = PosOrder::query()
            ->whereIn('provider_payment_id', $ids)
            ->orWhereIn('payment_intent_id', $ids)
            ->get(['provider_payment_id', 'payment_intent_id'])
            ->flatMap(fn ($o) => [$o->provider_payment_id, $o->payment_intent_id])
            ->filter()
            ->flip();

        $orphans = array_values(array_filter($remote, function ($r) use ($known) {
            $id = (string) ($r['id'] ?? '');

            return $id !== '' && !$known->has($id);
        }));

        if (empty($orphans)) {
            $this->newLine();
            $this->info('Every provider transaction in that window has a matching order.');

            return self::SUCCESS;
        }

        $this->line('Fetching tip detail for ' . count($orphans) . ' unmatched payment(s)...');

        $rows         = [];
        $missingCents = 0;
        $tipsUnknown  = 0;
        $client       = new PlutoPayClient();

        foreach ($orphans as $r) {
            $id     = (string) $r['id'];
            $amount = (int) ($r['amount'] ?? 0);
            $status = strtolower((string) ($r['status'] ?? ''));
            $when   = $r['created_at'] ?? ($r['created'] ?? ($r['date'] ?? null));
            $local  = $when ? Carbon::parse($when)->setTimezone($tz) : null;

            // List rows carry no tip. The provider reports tips only on the
            // terminal event, the same way it reports metadata, so a $0.00
            // read from the list is silence rather than zero — and printing
            // it as zero would understate what a barber is owed.
            $tip = null;
            try {
                $detail = $client->retrievePayment($id);
                if ($detail !== null) {
                    $amount = $detail['amount'] ?? $amount;
                    $tip    = $detail['tip_amount'];
                }
            } catch (PlutoPayException $e) {
                // Leave the tip unknown rather than guessing at it.
            }

            if ($tip === null) {
                $tipsUnknown++;
            }

            if (in_array($status, ['succeeded', 'completed', 'paid', 'captured'], true)) {
                $missingCents += $amount + (int) $tip;
            }

            $rows[] = [
                $id,
                $status,
                '$' . number_format($amount / 100, 2),
                $tip === null ? '?' : '$' . number_format($tip / 100, 2),
                $tip === null ? '?' : '$' . number_format(($amount + $tip) / 100, 2),
                $local?->format('M j H:i') ?? '-',
                $local?->copy()->utc()->format('M j H:i') ?? '-',
            ];
        }

        $this->newLine();

        if (empty($rows)) {
            $this->info('Every provider transaction in that window has a matching order.');

            return self::SUCCESS;
        }

        $this->warn(count($rows) . ' provider payment(s) with no order on our side:');
        $this->table(
            ['provider id', 'status', 'amount', 'tip', 'captured', 'shop time', 'UTC'],
            $rows
        );

        $this->newLine();
        $this->error(
            ($tipsUnknown > 0 ? 'Unrecorded takings, AT LEAST: $' : 'Unrecorded takings: $')
            . number_format($missingCents / 100, 2)
        );
        if ($tipsUnknown > 0) {
            $this->warn(
                "Tips could not be confirmed for {$tipsUnknown} payment(s) — the real figure is higher. "
                . 'Ask the provider for the tip on each id before paying anyone.'
            );
        }
        $this->line('These need an employee attached before payroll is correct.');
        $this->comment('Read-only — no orders were created.');

        return self::SUCCESS;
    }
}
