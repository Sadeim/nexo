<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ledger of payouts to employees. `paid_at` is the accounting date (defaults
 * to now on create). `period_from`/`period_to` optionally record which
 * work-period the payment covers so a report can attribute it correctly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_payments');
    }
};
