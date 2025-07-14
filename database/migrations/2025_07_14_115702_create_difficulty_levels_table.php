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
        Schema::create('difficulty_levels', function (Blueprint $table) {//Độ khó: Dễ, Trung Bình, Khó
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->tinyInteger('status')->default(0);
            $table->integer('sort_order')->default(9999);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('difficulty_levels');
    }
};
