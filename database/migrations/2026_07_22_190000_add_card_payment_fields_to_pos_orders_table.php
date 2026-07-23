<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extra columns the Flutter POS card flow needs to represent a sale that is
 * not settled instantly (awaiting_payment → processing → completed | failed).
 * Existing cash rows keep working: `status` defaults to `completed`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->enum('status', [
                'completed',
                'awaiting_payment',
                'processing',
                'failed',
                'canceled',
            ])->default('completed')->after('payment_method');

            $table->string('payment_intent_id')->nullable()->after('status');
            $table->string('provider_payment_id')->nullable()->after('payment_intent_id');
            $table->string('reference')->nullable()->after('provider_payment_id');
            $table->unsignedInteger('amount_cents')->nullable()->after('reference');
            $table->string('currency', 3)->default('usd')->after('amount_cents');
            $table->string('failure_reason')->nullable()->after('currency');
            $table->string('idempotency_key', 64)->nullable()->unique()->after('failure_reason');

            $table->index('payment_intent_id');
            $table->index('provider_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropIndex(['payment_intent_id']);
            $table->dropIndex(['provider_payment_id']);
            $table->dropColumn([
                'status',
                'payment_intent_id',
                'provider_payment_id',
                'reference',
                'amount_cents',
                'currency',
                'failure_reason',
                'idempotency_key',
            ]);
        });
    }
};
