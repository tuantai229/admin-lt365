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
        Schema::create('documents', function (Blueprint $table) {//Tài liệu
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('featured_image_id')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('file_type')->nullable();
            $table->integer('price')->default(0);
            $table->integer('download_count')->default(0);
            $table->unsignedBigInteger('level_id')->default(0);
            $table->unsignedBigInteger('subject_id')->default(0);
            $table->unsignedBigInteger('document_type_id')->default(0);
            $table->unsignedBigInteger('difficulty_level_id')->default(0);
            $table->unsignedBigInteger('school_id')->default(0);
            $table->year('year')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->tinyInteger('status')->default(0);
            $table->integer('sort_order')->default(9999);
            $table->unsignedBigInteger('admin_user_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
