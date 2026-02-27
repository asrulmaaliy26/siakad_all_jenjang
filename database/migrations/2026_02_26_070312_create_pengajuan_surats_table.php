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
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_riwayat_pendidikan')->constrained('riwayat_pendidikan')->onDelete('cascade');
            $table->foreignId('id_tahun_akademik')->constrained('tahun_akademik')->onDelete('cascade');
            $table->string('jenis_surat'); // cuti, rekomendasi_seminar, ket_aktif, ket_lulus, dll
            $table->text('keperluan');
            $table->string('status')->default('diajukan'); // diajukan, diproses, disetujui, ditolak, selesai
            $table->text('catatan_admin')->nullable();
            $table->string('file_pendukung')->nullable(); // Upload dari mahasiswa
            $table->string('file_hasil')->nullable(); // Upload dari admin (surat jadi)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surats');
    }
};
