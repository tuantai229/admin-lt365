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
        Schema::create('meta_seo', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100);                    // 'documents', 'schools', 'news'
            $table->unsignedBigInteger('type_id');          // ID của record tương ứng
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('meta_robots', 100)->default('index,follow');
            $table->unsignedBigInteger('og_image_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            
            $table->unique(['type', 'type_id']);
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_seo');
    }
};
