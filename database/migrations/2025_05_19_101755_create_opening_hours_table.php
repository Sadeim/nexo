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
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_id')->constrained()->onDelete('cascade');
            $table->string('day'); // مثال: "Monday"
            $table->string('from')->nullable(); // وقت البداية
            $table->string('to')->nullable();   // وقت النهاية
            $table->boolean('is_closed')->default(false); // مغلق
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_hours');
    }
};
