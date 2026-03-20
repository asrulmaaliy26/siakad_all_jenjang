<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('library_procurement_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_procurement_id');
            $table->unsignedBigInteger('library_book_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2)->default('0.00');
            $table->timestamps();
            $table->foreign('library_book_id')->references('id')->on('library_books')->onDelete('cascade');
            $table->foreign('library_procurement_id')->references('id')->on('library_procurements')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('library_procurement_details'); } 
};
