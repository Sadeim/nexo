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
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image1');
            $table->string('image2')->nullable();
            $table->string('tab1_title');
            $table->text('tab1_content');
            $table->string('tab2_title');
            $table->text('tab2_content');
            $table->string('tab3_title');
            $table->text('tab3_content');
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
