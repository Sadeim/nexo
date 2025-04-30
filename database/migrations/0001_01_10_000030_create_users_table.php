<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->datetime('email_verified_at')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('phone_code')->default('966');
            $table->string('phone');
            $table->string('otp')->nullable();
            $table->datetime('otp_expired_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('image')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
