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
        Schema::create('hows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->enum('is_featured', ['1', '0'])->default('1');
            $table->string('tap1_name');
            $table->string('tap1_number');
            $table->string('tap1_content');
            $table->string('tap2_name');
            $table->string('tap2_number');
            $table->string('tap2_content');
            $table->string('tap3_name');
            $table->string('tap3_number');
            $table->string('tap3_content');
            $table->string('tap4_name');
            $table->string('tap4_number');
            $table->string('tap4_content');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hows');
    }
};
