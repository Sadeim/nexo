<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * pos_order_items.service_id used to FK-reference the marketing `services`
 * table. The POS now ships its own `pos_services` catalog, so the old FK
 * would reject new inserts. Drop the FK and keep the column as a loose
 * reference (nullable unsigned bigint) — it just records "which service was
 * this line", either from pos_services now or from the legacy services table
 * on historical rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_order_items', function (Blueprint $table) {
            try {
                $table->dropForeign(['service_id']);
            } catch (\Throwable $e) {
                // FK might not exist on some environments — that's fine.
            }
        });
    }

    public function down(): void
    {
        Schema::table('pos_order_items', function (Blueprint $table) {
            try {
                $table->foreign('service_id')
                    ->references('id')->on('services')
                    ->nullOnDelete();
            } catch (\Throwable $e) {
                //
            }
        });
    }
};
