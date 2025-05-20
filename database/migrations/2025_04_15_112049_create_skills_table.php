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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            // $table->string('title');
            // $table->string('description');
            $table->integer('percent1');  // مثلاً 96, 92, 94
            $table->string('text1');      // e.g., "Technical Knowledge"
            $table->integer('percent2');  // مثلاً 96, 92, 94
            $table->string('text2');      // e.g., "Technical Knowledge"
            $table->integer('percent3');  // مثلاً 96, 92, 94
            $table->string('text3');      // e.g., "Technical Knowledge"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
