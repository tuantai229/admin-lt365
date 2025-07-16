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
        Schema::create('school_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('year');
            $table->integer('total_students')->nullable();
            $table->integer('number_of_classes')->nullable();
            $table->integer('students_per_class')->nullable();
            $table->decimal('estimated_tuition_fee', 10, 2)->nullable();
            $table->string('program_type')->nullable();
            $table->date('register_start_date')->nullable();
            $table->date('register_end_date')->nullable();
            $table->date('exam_date')->nullable();
            $table->date('result_announcement_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_admissions');
    }
};
