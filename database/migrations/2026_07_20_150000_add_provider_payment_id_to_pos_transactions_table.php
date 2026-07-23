<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PlutoPay's create-payment returns TWO identifiers:
 *   - data.payment_intent_id (pi_...)  -> what process-payment requires, and
 *     the likely value on the webhook.
 *   - data.id (a provider UUID)        -> the record id; the webhook's data.id
 *     may carry this instead.
 *
 * We store both and match the webhook against either, so settlement works
 * regardless of which id PlutoPay puts on the event. `payment_intent_id`
 * already exists; this adds the provider UUID.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->string('provider_payment_id')->nullable()->after('payment_intent_id');
            $table->index('provider_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropIndex(['provider_payment_id']);
            $table->dropColumn('provider_payment_id');
        });
    }
};
