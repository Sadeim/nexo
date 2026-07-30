<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks a row as a STANDALONE TIP rather than a sale.
 *
 * Sometimes a customer hands an employee cash on the way out, after the sale
 * is already closed. The cashier records it so the employee's tip total is
 * right, but that money never entered the drawer — so these rows:
 *
 *   - carry subtotal 0 and no items,
 *   - are excluded from shop takings (Sales / Cashiers / Services reports),
 *   - DO count toward the employee's tips in payroll,
 *   - are treated as already paid, so they don't inflate the balance owed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->boolean('is_tip_only')->default(false)->after('payment_method');
            $table->index('is_tip_only');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropIndex(['is_tip_only']);
            $table->dropColumn('is_tip_only');
        });
    }
};
