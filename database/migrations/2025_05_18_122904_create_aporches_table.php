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
        Schema::create('aporches', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image1');
            $table->string('image2');
            $table->string('tap1_name');
            $table->string('tap1_content');
            $table->string('tap2_name');
            $table->string('tap2_content');
            $table->string('tap3_name');
            $table->string('tap3_content');
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aporches');
    }
};
