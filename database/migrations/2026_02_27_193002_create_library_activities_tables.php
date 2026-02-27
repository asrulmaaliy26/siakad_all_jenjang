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
        Schema::create('library_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riwayat_pendidikan_id')->constrained('riwayat_pendidikan')->cascadeOnDelete();
            $table->dateTime('visited_at');
            $table->string('purpose')->nullable();
            $table->timestamps();
        });

        Schema::create('library_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('riwayat_pendidikan_id')->constrained('riwayat_pendidikan')->cascadeOnDelete();
            $table->dateTime('borrowed_at');
            $table->dateTime('due_at');
            $table->dateTime('returned_at')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed');
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('library_loan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_loan_id')->constrained('library_loans')->cascadeOnDelete();
            $table->foreignId('library_book_id')->constrained('library_books')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_loan_details');
        Schema::dropIfExists('library_loans');
        Schema::dropIfExists('library_visits');
    }
};
