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
        Schema::create('school_admission_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('academic_year');
            $table->integer('target_quota')->nullable();
            $table->integer('registered_count')->nullable();
            $table->decimal('cutoff_score', 8, 2)->nullable();
            $table->decimal('cutoff_score_max', 8, 2)->nullable();
            $table->integer('sort_order')->default(9999);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_admission_stats');
    }
};
