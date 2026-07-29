<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Zelle joins cash and card as a POS payment method. It behaves like cash
 * (no reader, tip entered on the tablet) but is tracked separately so the
 * books show which money landed in the bank vs the drawer.
 *
 * Raw ALTER because Laravel's schema builder can't widen an ENUM without
 * doctrine/dbal, which this project doesn't install.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pos_orders')) {
            return;
        }

        DB::statement(
            "ALTER TABLE `pos_orders`
             MODIFY `payment_method` ENUM('cash','card','zelle') NOT NULL DEFAULT 'cash'"
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('pos_orders')) {
            return;
        }

        // Anything recorded as zelle would violate the narrowed enum — fold it
        // back into cash so the rollback can't fail on live data.
        DB::table('pos_orders')->where('payment_method', 'zelle')->update([
            'payment_method' => 'cash',
        ]);

        DB::statement(
            "ALTER TABLE `pos_orders`
             MODIFY `payment_method` ENUM('cash','card') NOT NULL DEFAULT 'cash'"
        );
    }
};
