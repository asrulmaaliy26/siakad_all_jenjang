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
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->nullable()->unique();
            $table->foreignId('library_author_id')->nullable()->constrained('library_authors')->nullOnDelete();
            $table->foreignId('library_publisher_id')->nullable()->constrained('library_publishers')->nullOnDelete();
            $table->foreignId('library_category_id')->nullable()->constrained('library_categories')->nullOnDelete();
            $table->integer('year')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('total_borrows')->default(0);
            $table->string('location')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
