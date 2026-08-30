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
        {--pages=10 : Max pages of 100 to pull — the provider allows 100 requests/minute}
        {--dump= : Print one transaction\'s raw payload and exit — use it to find a field we are not reading}';

    protected $description = 'Read-only: list provider card payments that have no matching order.';

    public function handle(): int
    {
        if ($dumpId = (string) $this->option('dump')) {
            try {
                $raw = (new PlutoPayClient())->retrievePaymentRaw($dumpId);
            } catch (PlutoPayException $e) {
                $this->error("Lookup failed: {$e->getMessage()}");

                return self::FAILURE;
            }

            if ($raw === null) {
                $this->warn('The provider does not recognise that id.');

                return self::SUCCESS;
            }

            $this->line(json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

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

        $this->line('Fetching detail for ' . count($orphans) . ' unmatched payment(s)...');

        $rows         = [];
        $missingCents = 0;
        $mislinked    = 0;
        $client       = new PlutoPayClient();

        foreach ($orphans as $r) {
            $id     = (string) $r['id'];
            $amount = (int) ($r['amount'] ?? 0);
            $status = strtolower((string) ($r['status'] ?? ''));
            $when   = $r['created_at'] ?? ($r['created'] ?? ($r['date'] ?? null));
            $local  = $when ? Carbon::parse($when)->setTimezone($tz) : null;

            // This endpoint reports no tip at all — not a zero, no field. So
            // the base amount is the most these rows can ever tell us, and
            // the tip has to be asked for by Stripe id. It does return the
            // metadata, though, which settles what kind of gap each row is.
            $processorId = '';
            $stampedWith = null;
            try {
                $detail = $client->retrievePayment($id);
                if ($detail !== null) {
                    $amount      = $detail['amount'] ?? $amount;
                    $processorId = $detail['processor_id'];
                    $stampedWith = $detail['metadata']['pos_order_id'] ?? null;
                }
            } catch (PlutoPayException $e) {
                // Fall back to what the list row gave us.
            }

            // A charge carrying our order id is a different failure from one
            // taken outside the app: the sale did start with us and we lost
            // the link to it. Same money missing, entirely different bug.
            if ($stampedWith !== null) {
                $mislinked++;
            }

            if (in_array($status, ['succeeded', 'completed', 'paid', 'captured'], true)) {
                $missingCents += $amount;
            }

            $rows[] = [
                $id,
                $status,
                '$' . number_format($amount / 100, 2),
                $processorId !== '' ? $processorId : '-',
                $stampedWith !== null ? "order {$stampedWith}" : 'none',
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
            ['provider id', 'status', 'amount', 'stripe id', 'our order id', 'shop time', 'UTC'],
            $rows
        );

        $this->newLine();
        $this->error('Unrecorded takings, BEFORE TIPS: $' . number_format($missingCents / 100, 2));
        $this->warn(
            'This endpoint does not report tips — no field, not a zero. Ask the provider '
            . 'for the tip on each Stripe id above before paying anyone.'
        );

        if ($mislinked > 0) {
            $this->newLine();
            $this->error(
                "{$mislinked} of these carry OUR order id in their metadata. Those sales did start "
                . 'in our app and we lost the link — a separate bug from a charge taken outside it.'
            );
        }

        $this->line('These need an employee attached before payroll is correct.');
        $this->comment('Read-only — no orders were created.');

        return self::SUCCESS;
    }
}
