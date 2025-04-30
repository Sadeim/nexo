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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الفريق أو العضو
            $table->string('position')->nullable(); // الوظيفة
            $table->string('image')->nullable(); // مسار الصورة
            $table->json('social_links')->nullable(); // روابط التواصل (Facebook, Instagram, LinkedIn, Twitter) بصيغة JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
