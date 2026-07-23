<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Percent of a service's SUBTOTAL that goes to the employee. Tips are always
 * paid 100% to the employee on top of this. A rate of 0 means "tips only".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(0)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};
