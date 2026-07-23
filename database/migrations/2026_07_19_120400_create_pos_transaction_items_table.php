<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * POS sale line items. We snapshot the service name and its original price at
 * the moment of sale so the invoice stays historically correct even if the
 * service is later re-priced, renamed, or deleted. The custom (per-transaction)
 * price lives in `unit_price` and NEVER touches the services table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_transaction_id')
                ->constrained('pos_transactions')
                ->cascadeOnDelete();

            // Keep the link for reporting, but the snapshot columns are the
            // source of truth for money. Nullable so a deleted service does
            // not break historical invoices.
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->string('service_name');                 // snapshot
            $table->decimal('original_price', 10, 2)->nullable(); // snapshot of the stored price
            $table->decimal('unit_price', 10, 2);           // actual price charged (custom or original)
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 10, 2);           // unit_price * quantity

            $table->timestamps();

            $table->index('pos_transaction_id');
            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaction_items');
    }
};
