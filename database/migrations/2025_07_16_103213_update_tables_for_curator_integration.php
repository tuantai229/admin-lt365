<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('slug');
            $table->foreign('featured_image_id')->references('id')->on('media')->onDelete('set null');
        });

        // Update news table
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('slug');
            $table->foreign('featured_image_id')->references('id')->on('media')->onDelete('set null');
        });

        // Update schools table
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('slug');
            $table->foreign('featured_image_id')->references('id')->on('media')->onDelete('set null');
        });

        // Update centers table
        Schema::table('centers', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('slug');
            $table->foreign('featured_image_id')->references('id')->on('media')->onDelete('set null');
        });

        // Update teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('slug');
            $table->foreign('featured_image_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropColumn('featured_image_id');
            $table->text('image')->nullable()->after('slug');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropColumn('featured_image_id');
            $table->string('image')->nullable()->after('slug');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropColumn('featured_image_id');
            $table->string('image')->nullable()->after('slug');
        });

        Schema::table('centers', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropColumn('featured_image_id');
            $table->string('image')->nullable()->after('slug');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropColumn('featured_image_id');
            $table->string('image')->nullable()->after('slug');
        });
    }
};