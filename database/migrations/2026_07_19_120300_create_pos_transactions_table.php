<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * POS sale header. One row per completed (or pending) transaction.
 * The employee is taken from the authenticated admin session — there is no
 * "select employee" UI, so `admin_id` is always the logged-in cashier.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_id')
                ->constrained('admins')
                ->restrictOnDelete();

            // Money is always DECIMAL, never float.
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);

            // 'card' is structural only for now (payment logic lands later).
            $table->enum('payment_method', ['cash', 'card'])->default('cash');

            // 'card' can later be left 'pending' until the gateway confirms.
            $table->enum('status', ['completed', 'pending'])->default('completed');

            // Reserved for a future card-payment gateway reference.
            $table->string('reference')->nullable();

            $table->timestamps();

            $table->index('admin_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};
