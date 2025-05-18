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
        Schema::create('approachs', function (Blueprint $table) {
            $table->id();

            $table->string('title')->default('Our Approach');
            $table->string('subtitle')->nullable();
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();

            $table->text('mission_description')->nullable();
            $table->json('mission_points')->nullable();

            $table->text('vision_description')->nullable();
            $table->json('vision_points')->nullable();

            $table->text('value_description')->nullable();
            $table->json('value_points')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approachs');
    }
};
