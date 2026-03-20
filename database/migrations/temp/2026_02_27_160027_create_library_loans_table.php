<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('library_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('riwayat_pendidikan_id');
            $table->dateTime('borrowed_at');
            $table->dateTime('due_at');
            $table->dateTime('returned_at')->nullable()->default(null);
            $table->enum('status', ['borrowed','returned','overdue','lost'])->default('borrowed');
            $table->decimal('fine_amount', 12, 2)->default('0.00');
            $table->unsignedBigInteger('staff_id')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('riwayat_pendidikan_id')->references('id')->on('riwayat_pendidikan')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('library_loans'); } 
};
