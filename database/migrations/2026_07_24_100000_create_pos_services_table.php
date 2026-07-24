<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Services SHOWN IN THE POS. Deliberately separate from the marketing
 * `services` table so POS prices can be raised without touching the public
 * site (Nexo's request 2026-07-24).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_services');
    }
};
