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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('image')->nullable();
            $table->string('tagline')->nullable();
            $table->integer('experience')->default(0);
            $table->text('address')->nullable();
            $table->unsignedBigInteger('province_id')->default(0);
            $table->unsignedBigInteger('commune_id')->default(0);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->longText('content')->nullable();
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
        Schema::dropIfExists('teachers');
    }
};
