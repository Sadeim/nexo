<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Card surcharge ADDED on top of the services on a card sale.
 *
 * The customer is charged subtotal + card_fee (+ any on-reader tip), so the
 * processor really does receive the higher amount. The employee still earns
 * on the full service subtotal — the surcharge is the shop's, not a deduction.
 *
 * Cash and Zelle never carry a fee.
 *
 * Snapshotted per order so changing the setting later can't rewrite history.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->decimal('card_fee', 10, 2)->default(0)->after('tip');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn('card_fee');
        });
    }
};
