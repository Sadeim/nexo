<?php

namespace App\Console\Commands;

use App\Models\PosOrder;
use Illuminate\Console\Command;

/**
 * Cash tips were dropped from the POS on 2026-07-25. New cash orders always
 * store tip = 0, but rows created before that still carry a cash tip. This
 * command zeroes them and re-derives total = subtotal.
 *
 * Destructive: it rewrites historical takings, so it asks for confirmation
 * and supports --dry-run.
 *
 *   php artisan nexo-pos:clear-cash-tips --dry-run
 *   php artisan nexo-pos:clear-cash-tips
 */
class ClearCashTips extends Command
{
    protected $signature = 'nexo-pos:clear-cash-tips
        {--dry-run : Show what would change without writing}';

    protected $description = 'Zero the tip on historical CASH orders and reset their total to the subtotal.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $orders = PosOrder::query()
            ->where('payment_method', 'cash')
            ->where('tip', '>', 0)
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No cash order carries a tip — nothing to do.');
            return self::SUCCESS;
        }

        $rows = [];
        $removed = 0.0;
        foreach ($orders as $o) {
            $removed += (float) $o->tip;
            $rows[] = [
                $o->id,
                $o->order_number,
                $o->created_at->format('Y-m-d H:i'),
                number_format((float) $o->subtotal, 2),
                number_format((float) $o->tip, 2),
                number_format((float) $o->total, 2),
                number_format((float) $o->subtotal, 2),
            ];
        }

        $this->warn($orders->count() . ' cash order(s) carry a tip:');
        $this->table(
            ['ID', 'Order #', 'Date', 'Subtotal', 'Tip (removed)', 'Total (old)', 'Total (new)'],
            $rows
        );
        $this->line('Tips that will be removed from the books: $' . number_format($removed, 2));

        if ($dryRun) {
            $this->comment('Dry run — nothing written.');
            return self::SUCCESS;
        }

        if (!$this->confirm('This rewrites past takings. Continue?', false)) {
            $this->comment('Aborted.');
            return self::SUCCESS;
        }

        foreach ($orders as $o) {
            $o->forceFill([
                'tip'   => 0,
                'total' => round((float) $o->subtotal, 2),
            ])->save();
        }

        $this->info('Cleared cash tips on ' . $orders->count() . ' order(s).');

        return self::SUCCESS;
    }
}
