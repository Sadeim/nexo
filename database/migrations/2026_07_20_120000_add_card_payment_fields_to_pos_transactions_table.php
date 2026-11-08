<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Card-payment (PlutoPay Terminal) fields for pos_transactions.
 *
 * Additive only — no existing column is dropped, no data is touched. The
 * `status` enum is WIDENED to carry the card lifecycle; existing 'completed'
 * / 'pending' values remain valid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            // PlutoPay payment intent id (create-payment -> data.id). The webhook
            // and reconciliation both key off this.
            $table->string('payment_intent_id')->nullable()->after('reference');

            // Explicit currency sent to the reader.
            $table->string('currency', 3)->default('usd')->after('payment_intent_id');

            // Exact integer cents sent to PlutoPay (audit / reconciliation).
            $table->unsignedInteger('amount_cents')->nullable()->after('currency');

            // Decline / failure reason from a payment.failed webhook.
            $table->string('failure_reason')->nullable()->after('amount_cents');

            // Client-supplied idempotency token for the whole checkout attempt.
            // UNIQUE => a double-clicked "Card" maps to ONE transaction row, and
            // the same value is reused as the PlutoPay Idempotency-Key.
            $table->string('idempotency_key')->nullable()->unique()->after('failure_reason');

            $table->index('payment_intent_id');
        });

        // Widen the status column for the card lifecycle. Default stays
        // 'completed' (the cash path).
        //
        // MySQL has no portable way to alter an ENUM, so it needs raw SQL.
        // SQLite has no ENUM at all — Laravel emits a CHECK constraint
        // instead — and MODIFY is not SQLite syntax, so the statement below
        // used to abort any migration run on SQLite. That is why the test
        // suite could not use an in-memory database. On SQLite the column is
        // redefined as a plain string, which drops the narrow CHECK and
        // accepts the same values.
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('pos_transactions', function (Blueprint $table) {
                $table->string('status', 32)->default('completed')->change();
            });
        } else {
            DB::statement(
                "ALTER TABLE pos_transactions MODIFY status "
                . "ENUM('completed','pending','awaiting_payment','processing','failed','canceled') "
                . "NOT NULL DEFAULT 'completed'"
            );
        }
    }

    public function down(): void
    {
        // Revert any card-lifecycle rows to a safe legacy value before shrinking.
        DB::statement(
            "UPDATE pos_transactions SET status = 'pending' "
            . "WHERE status IN ('awaiting_payment','processing','failed','canceled')"
        );
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('pos_transactions', function (Blueprint $table) {
                $table->string('status', 32)->default('completed')->change();
            });
        } else {
            DB::statement(
                "ALTER TABLE pos_transactions MODIFY status "
                . "ENUM('completed','pending') NOT NULL DEFAULT 'completed'"
            );
        }

        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropIndex(['payment_intent_id']);
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn(['payment_intent_id', 'currency', 'amount_cents', 'failure_reason', 'idempotency_key']);
        });
    }
};
