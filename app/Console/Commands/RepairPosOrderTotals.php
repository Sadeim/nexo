<?php

namespace App\Console\Commands;

use App\Models\PosOrder;
use Illuminate\Console\Command;

/**
 * Backfill for orders whose `total` doesn't equal subtotal + tip.
 *
 * Card sales settled before 2026-07-25 stored `total` from PlutoPay's
 * `data.amount`, which is the amount the intent was CREATED with (the base) —
 * so the on-reader tip was recorded in `tip` but never rolled into `total`.
 * Reports summed the wrong column and Gross came out short by the card tips.
 *
 * Usage:
 *   php artisan nexo-pos:repair-totals --dry-run
 *   php artisan nexo-pos:repair-totals
 */
class RepairPosOrderTotals extends Command
{
    protected $signature = 'nexo-pos:repair-totals
        {--dry-run : Show what would change without writing}';

    protected $description = 'Recompute pos_orders.total as subtotal + tip where they disagree.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $mismatched = PosOrder::query()
            ->whereRaw('ROUND(total, 2) <> ROUND(subtotal + tip, 2)')
            ->orderBy('id')
            ->get();

        if ($mismatched->isEmpty()) {
            $this->info('Nothing to repair — every order already has total = subtotal + tip.');
            return self::SUCCESS;
        }

        $this->warn($mismatched->count() . ' order(s) have a total that disagrees with subtotal + tip:');

        $rows = [];
        $delta = 0.0;
        foreach ($mismatched as $o) {
            $correct = round((float) $o->subtotal + (float) $o->tip, 2);
            $delta += $correct - (float) $o->total;
            $rows[] = [
                $o->id,
                $o->order_number,
                $o->payment_method,
                number_format((float) $o->subtotal, 2),
                number_format((float) $o->tip, 2),
                number_format((float) $o->total, 2),
                number_format($correct, 2),
            ];
        }

        $this->table(
            ['ID', 'Order #', 'Method', 'Subtotal', 'Tip', 'Total (old)', 'Total (fixed)'],
            $rows
        );
        $this->line('Net change to reported gross: $' . number_format($delta, 2));

        if ($dryRun) {
            $this->comment('Dry run — nothing written. Re-run without --dry-run to apply.');
            return self::SUCCESS;
        }

        $fixed = 0;
        foreach ($mismatched as $o) {
            $o->forceFill([
                'total' => round((float) $o->subtotal + (float) $o->tip, 2),
            ])->save();
            $fixed++;
        }

        $this->info("Repaired {$fixed} order(s).");

        return self::SUCCESS;
    }
}
