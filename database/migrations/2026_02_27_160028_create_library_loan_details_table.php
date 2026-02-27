<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('library_loan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_loan_id');
            $table->unsignedBigInteger('library_book_id');
            $table->timestamps();
            $table->foreign('library_book_id')->references('id')->on('library_books')->onDelete('cascade');
            $table->foreign('library_loan_id')->references('id')->on('library_loans')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('library_loan_details'); } 
};
