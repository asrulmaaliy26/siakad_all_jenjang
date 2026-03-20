<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('siswa_data_pendaftar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa_data');
            $table->string('Nama_Lengkap', 255)->nullable()->default(null);
            $table->string('No_Pendaftaran', 255)->nullable()->default(null);
            $table->string('Tgl_Daftar', 255)->nullable()->default(null);
            $table->unsignedBigInteger('ro_program_sekolah')->nullable()->default(null);
            $table->string('Kelas_Program_Kuliah', 50)->nullable()->default(null);
            $table->unsignedBigInteger('id_jurusan')->nullable()->default(null);
            $table->string('Prodi_Pilihan_1', 255)->nullable()->default(null);
            $table->string('Prodi_Pilihan_2', 255)->nullable()->default(null);
            $table->string('Jalur_PMB', 50)->nullable()->default(null);
            $table->string('Bukti_Jalur_PMB', 255)->nullable()->default(null);
            $table->string('Jenis_Pembiayaan', 255)->nullable()->default(null);
            $table->string('Bukti_Jenis_Pembiayaan', 255)->nullable()->default(null);
            $table->string('NIMKO_Asal', 255)->nullable()->default(null);
            $table->string('Prodi_Asal', 255)->nullable()->default(null);
            $table->string('PT_Asal', 255)->nullable()->default(null);
            $table->string('Jml_SKS_Asal', 255)->nullable()->default(null);
            $table->string('IPK_Asal', 255)->nullable()->default(null);
            $table->string('Semester_Asal', 255)->nullable()->default(null);
            $table->string('Pengantar_Mutasi', 255)->nullable()->default(null);
            $table->string('Transkip_Asal', 255)->nullable()->default(null);
            $table->string('Legalisir_Ijazah', 255)->nullable()->default(null);
            $table->string('Legalisir_SKHU', 255)->nullable()->default(null);
            $table->string('Copy_KTP', 255)->nullable()->default(null);
            $table->string('Foto_BW_3x3', 255)->nullable()->default(null);
            $table->string('Foto_BW_3x4', 255)->nullable()->default(null);
            $table->string('Foto_Warna_5x6', 255)->nullable()->default(null);
            $table->string('File_Foto_Berwarna', 255)->nullable()->default(null);
            $table->string('Nama_File_Foto', 255)->nullable()->default(null);
            $table->string('Tgl_Tes_Tulis', 255)->nullable()->default(null);
            $table->string('N_Agama', 255)->nullable()->default(null);
            $table->string('N_Umum', 255)->nullable()->default(null);
            $table->string('N_Psiko', 255)->nullable()->default(null);
            $table->string('N_Jumlah_Tes_Tulis', 255)->nullable()->default(null);
            $table->string('N_Rerata_Tes_Tulis', 255)->nullable()->default(null);
            $table->string('Tgl_Tes_Lisan', 255)->nullable()->default(null);
            $table->string('N_Potensi_Akademik', 255)->nullable()->default(null);
            $table->string('N_Baca_al_Quran', 255)->nullable()->default(null);
            $table->string('N_Baca_Kitab_Kuning', 255)->nullable()->default(null);
            $table->string('N_Jumlah_Tes_Lisan', 255)->nullable()->default(null);
            $table->string('N_Rearata_Tes_Lisan', 255)->nullable()->default(null);
            $table->string('Jumlah_Nilai', 255)->nullable()->default(null);
            $table->string('Rata_Rata', 255)->nullable()->default(null);
            $table->string('Rekomendasi_1', 255)->nullable()->default(null);
            $table->string('Rekomendasi_2', 255)->nullable()->default(null);
            $table->string('No_SK_Kelulusan', 255)->nullable()->default(null);
            $table->string('Tgl_SK_Kelulusan', 255)->nullable()->default(null);
            $table->string('Diterima_di_Prodi', 255)->nullable()->default(null);
            $table->string('Biaya_Pendaftaran', 255)->nullable()->default(null);
            $table->string('Bukti_Biaya_Daftar', 255)->nullable()->default(null);
            $table->string('verifikator', 255)->nullable()->default(null);
            $table->string('referral', 20)->nullable()->default(null);
            $table->enum('status_valid', ['0','1'])->nullable()->default('0');
            $table->enum('Status_Pendaftaran', ['Y','N','B'])->nullable()->default('B');
            $table->enum('Status_Kelulusan_Seleksi', ['Y','N','B'])->nullable()->default('B');
            $table->unsignedBigInteger('id_referal_code')->nullable()->default(null);
            $table->unsignedBigInteger('id_tahun_akademik')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('id_siswa_data')->references('id')->on('siswa_data')->onDelete('cascade');
            $table->foreign('id_referal_code')->references('id')->on('referal_codes')->onDelete('set null');
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademik')->onDelete('set null');
            $table->foreign('id_jurusan')->references('id')->on('jurusan');
            $table->foreign('ro_program_sekolah')->references('id')->on('reference_option');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('siswa_data_pendaftar'); } 
};
