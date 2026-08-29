<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Read-only snapshot of the card picture across BOTH POS systems.
 *
 * The provider reports on one shared endpoint and cannot tell our tablet
 * orders (pos_orders, order numbers like N260828FBF02) apart from the web
 * POS's transactions (pos_transactions, plain auto-increment ids). When they
 * hand us a list of "missing" payments, this is what says which books it
 * actually belongs to — and whether the tablet's card flow has ever settled
 * a sale at all.
 *
 * Writes nothing. Safe to run at any time.
 */
class DiagnoseNexoPosCards extends Command
{
    protected $signature = 'nexo-pos:card-diagnostics
        {--date=2026-08-28 : Day to inspect in detail (UTC)}
        {--from=856 : First web-POS id to check}
        {--to=888 : Last web-POS id to check}';

    protected $description = 'Read-only: compare tablet POS and web POS card activity.';

    public function handle(): int
    {
        $date = (string) $this->option('date');
        $from = (int) $this->option('from');
        $to   = (int) $this->option('to');

        $this->newLine();
        $this->info('1. Tablet POS (pos_orders) — every card order by status');
        $this->line('   If "completed" is zero, the tablet has never settled a card sale.');
        $rows = DB::table('pos_orders')
            ->where('payment_method', 'card')
            ->selectRaw('status, COUNT(*) AS n, ROUND(SUM(total), 2) AS total')
            ->groupBy('status')
            ->orderBy('status')
            ->get();
        $this->table(['status', 'orders', 'total'], $rows->map(fn ($r) => (array) $r)->all());

        $this->newLine();
        $this->info("2. Tablet POS (pos_orders) — ids {$from}-{$to}, the numbers the provider quoted");
        $this->line('   We send pos_orders.id as metadata, NOT the N… order number, so the');
        $this->line('   provider\'s sequential numbers are these rows. Anything still pending');
        $this->line('   here that the provider called successful is a real contradiction.');
        $rows = DB::table('pos_orders')
            ->whereBetween('id', [$from, $to])
            ->orderBy('id')
            ->get(['id', 'order_number', 'payment_method', 'status', 'total', 'created_at']);
        $this->renderOrEmpty($rows, ['id', 'order_number', 'method', 'status', 'total', 'created (shop)', 'created (UTC)']);

        $this->newLine();
        $this->info("3. Tablet POS (pos_orders) — card activity on {$date} UTC");
        $rows = DB::table('pos_orders')
            ->where('payment_method', 'card')
            ->whereBetween('created_at', ["{$date} 00:00:00", "{$date} 23:59:59"])
            ->orderBy('created_at')
            ->get(['id', 'order_number', 'status', 'total', 'created_at']);
        $this->renderOrEmpty($rows, ['id', 'order_number', 'status', 'total', 'created (shop)', 'created (UTC)']);

        $this->newLine();
        $this->info('4. Web POS (pos_transactions) — is it even in use?');
        $wp = DB::table('pos_transactions')
            ->selectRaw('COUNT(*) AS rows_total, MAX(id) AS max_id, MAX(created_at) AS last_seen')
            ->first();
        $this->table(
            ['rows', 'max id', 'last activity'],
            [[$wp->rows_total ?? 0, $wp->max_id ?? '-', $wp->last_seen ?? '-']]
        );

        $this->newLine();
        $this->comment('Read-only — nothing was written.');

        return self::SUCCESS;
    }

    private function renderOrEmpty($rows, array $headers): void
    {
        if ($rows->isEmpty()) {
            $this->warn('   (none found)');
            return;
        }

        // created_at is stored in the shop's own timezone (APP_TIMEZONE), and
        // a raw query builder hands it back as an unconverted string. The
        // provider quotes everything in UTC, so print both — labelling shop
        // time as UTC would put every comparison five hours out.
        $tz = config('app.timezone');

        $this->table($headers, $rows->map(function ($r) use ($tz) {
            $row   = (array) $r;
            $local = Carbon::parse($row['created_at'], $tz);
            $row['created_at'] = $local->format('M j H:i');
            $row['created_utc'] = $local->copy()->utc()->format('M j H:i');

            return $row;
        })->all());
    }
}
