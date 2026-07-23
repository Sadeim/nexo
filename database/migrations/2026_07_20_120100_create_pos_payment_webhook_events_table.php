<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit log + idempotency ledger for inbound PlutoPay webhooks.
 *
 * The UNIQUE(delivery_id) is the database-level guarantee that a redelivered
 * webhook is processed at most once. The raw payload is stored for later
 * reconciliation/forensics.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_payment_webhook_events', function (Blueprint $table) {
            $table->id();

            // X-PlutoPay-Delivery (fallback: payload id). Unique => de-dupe.
            $table->string('delivery_id')->unique();

            $table->string('event_type');
            $table->string('payment_intent_id')->nullable()->index();

            // Raw decoded payload kept for audit / manual reconciliation.
            $table->json('payload');

            // Null until the business effect has been applied.
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_payment_webhook_events');
    }
};
