<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bearer tokens for the Flutter POS mobile app. Separate from the web POS
 * (which uses session auth on the `pos` guard).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('POS Terminal');
            $table->string('token', 80)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_api_tokens');
    }
};
