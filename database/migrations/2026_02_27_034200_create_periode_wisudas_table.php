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
        Schema::create('periode_wisudas', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->integer('periode_ke'); // 1, 2, 3, etc.
            $table->integer('kuota')->default(800);
            $table->integer('pendaftar_count')->default(0);
            $table->enum('status', ['Buka', 'Tutup', 'Belum Dibuka'])->default('Belum Dibuka');
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_wisudas');
    }
};
