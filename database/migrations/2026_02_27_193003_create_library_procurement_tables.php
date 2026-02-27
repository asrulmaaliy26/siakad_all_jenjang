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
        Schema::create('library_procurements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('vendor')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->dateTime('procurement_date');
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('library_procurement_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_procurement_id')->constrained('library_procurements')->cascadeOnDelete();
            $table->foreignId('library_book_id')->constrained('library_books')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_procurement_details');
        Schema::dropIfExists('library_procurements');
    }
};
