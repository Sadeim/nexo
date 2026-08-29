<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        $this->info("2. Web POS (pos_transactions) — card activity on {$date} UTC");
        $this->line('   These are the ones the provider may be reporting as ours.');
        $rows = DB::table('pos_transactions')
            ->whereBetween('created_at', ["{$date} 00:00:00", "{$date} 23:59:59"])
            ->orderBy('created_at')
            ->get(['id', 'payment_method', 'status', 'total', 'created_at']);
        $this->renderOrEmpty($rows, ['id', 'method', 'status', 'total', 'created_at (UTC)']);

        $this->newLine();
        $this->info("3. Web POS (pos_transactions) — ids {$from}–{$to}");
        $this->line('   The provider quoted sequential numbers; ours are never sequential.');
        $rows = DB::table('pos_transactions')
            ->whereBetween('id', [$from, $to])
            ->orderBy('id')
            ->get(['id', 'payment_method', 'status', 'total', 'created_at']);
        $this->renderOrEmpty($rows, ['id', 'method', 'status', 'total', 'created_at (UTC)']);

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
        $this->table($headers, $rows->map(fn ($r) => (array) $r)->all());
    }
}
