<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * At-most-once processing log for inbound webhooks aimed at the Flutter POS.
 * Separate from Haneen's pos_payment_webhook_events (Web POS).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_api_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_id')->unique();
            $table->string('event_type')->nullable();
            $table->string('payment_intent_id')->nullable()->index();
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_api_webhook_events');
    }
};
