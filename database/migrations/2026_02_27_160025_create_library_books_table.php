<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('isbn', 255)->nullable()->default(null);
            $table->unsignedBigInteger('library_author_id')->nullable()->default(null);
            $table->unsignedBigInteger('library_publisher_id')->nullable()->default(null);
            $table->unsignedBigInteger('library_category_id')->nullable()->default(null);
            $table->integer('year')->nullable()->default(null);
            $table->integer('stock')->default('0');
            $table->integer('total_borrows')->default('0');
            $table->string('location', 255)->nullable()->default(null);
            $table->string('cover_image', 255)->nullable()->default(null);
            $table->timestamps();
            $table->foreign('library_author_id')->references('id')->on('library_authors')->onDelete('set null');
            $table->foreign('library_category_id')->references('id')->on('library_categories')->onDelete('set null');
            $table->foreign('library_publisher_id')->references('id')->on('library_publishers')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('library_books'); } 
};
