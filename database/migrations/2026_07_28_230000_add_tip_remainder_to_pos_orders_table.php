<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cents skimmed off an on-reader card tip.
 *
 * House rule: a card tip is credited to the employee in WHOLE DOLLARS; the
 * fractional part goes to the shop alongside the card surcharge. A $12.70 tip
 * means the employee earns $12.00 and $0.70 joins the fees.
 *
 * Kept in its own column (rather than folded into card_fee) so settling stays
 * idempotent — the remainder is derived from the webhook's tip each time
 * instead of being accumulated onto an existing value — and so the dashboard
 * can still explain where the number came from.
 *
 * Cash and Zelle tips are entered by the cashier and are never rounded.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->decimal('tip_remainder', 10, 2)->default(0)->after('card_fee');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn('tip_remainder');
        });
    }
};
