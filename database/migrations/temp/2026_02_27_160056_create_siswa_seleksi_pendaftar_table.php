<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('siswa_seleksi_pendaftar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa_data_pendaftar');
            $table->string('nama_seleksi', 255)->nullable()->default(null);
            $table->dateTime('tanggal_seleksi')->nullable()->default(null);
            $table->text('deskripsi_seleksi')->nullable();
            $table->string('file_persyaratan', 255)->nullable()->default(null);
            $table->string('file_jawaban', 255)->nullable()->default(null);
            $table->string('nilai', 255)->nullable()->default(null);
            $table->enum('status_seleksi', ['B','Y','N'])->default('B')->comment('B: Pending, Y: Lulus/Sesuai, N: Tidak Lulus');
            $table->text('keterangan_admin')->nullable();
            $table->timestamps();
            $table->foreign('id_siswa_data_pendaftar')->references('id')->on('siswa_data_pendaftar')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('siswa_seleksi_pendaftar'); } 
};
