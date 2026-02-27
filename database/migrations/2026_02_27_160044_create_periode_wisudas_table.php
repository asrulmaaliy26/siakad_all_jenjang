<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('periode_wisudas', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->integer('periode_ke');
            $table->integer('kuota')->default('800');
            $table->integer('pendaftar_count')->default('0');
            $table->enum('status', ['Buka','Tutup','Belum Dibuka'])->default('Belum Dibuka');
            $table->date('tanggal_pelaksanaan')->nullable()->default(null);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('periode_wisudas'); } 
};
