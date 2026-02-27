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
        Schema::create('siswa_seleksi_pendaftar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa_data_pendaftar')->index();
            $table->string('nama_seleksi')->nullable(); // Tes Tulis, Psikotes, Wawancara, dll
            $table->dateTime('tanggal_seleksi')->nullable();
            $table->text('deskripsi_seleksi')->nullable(); // Persyaratan / Petunjuk
            $table->string('file_persyaratan')->nullable(); // File pendukung dari admin (PDF soal, dll)
            $table->string('file_jawaban')->nullable(); // File balasan/jurnal dari pendaftar
            $table->string('nilai')->nullable();
            $table->enum('status_seleksi', ['B', 'Y', 'N'])->default('B')->comment('B: Pending, Y: Lulus/Sesuai, N: Tidak Lulus');
            $table->text('keterangan_admin')->nullable();
            $table->timestamps();

            $table->foreign('id_siswa_data_pendaftar')
                ->references('id')
                ->on('siswa_data_pendaftar')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_seleksi_pendaftar');
    }
};
