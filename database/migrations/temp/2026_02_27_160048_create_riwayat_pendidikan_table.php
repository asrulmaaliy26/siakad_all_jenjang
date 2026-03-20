<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('riwayat_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa_data');
            $table->unsignedBigInteger('id_jurusan');
            $table->unsignedBigInteger('ro_program_sekolah')->nullable()->default(null)->comment('FK ke reference_option (program_sekolah)');
            $table->unsignedBigInteger('ro_status_siswa')->nullable()->default(null)->comment('FK ke reference_option (status_siswa)');
            $table->unsignedBigInteger('id_wali_dosen')->nullable()->default(null);
            $table->string('nomor_induk', 50)->nullable()->default(null);
            $table->date('tanggal_mulai')->nullable()->default(null);
            $table->date('tanggal_selesai')->nullable()->default(null);
            $table->string('foto_profil', 255)->nullable()->default(null);
            $table->integer('mulai_smt')->nullable()->default(null);
            $table->integer('smt_aktif')->nullable()->default(null);
            $table->string('dosen_wali', 255)->nullable()->default(null);
            $table->string('no_seri_ijazah', 100)->nullable()->default(null);
            $table->integer('sks_diakui')->nullable()->default(null);
            $table->string('jalur_skripsi', 100)->nullable()->default(null);
            $table->text('judul_skripsi')->nullable();
            $table->date('bln_awal_bimbingan')->nullable()->default(null);
            $table->date('bln_akhir_bimbingan')->nullable()->default(null);
            $table->string('sk_yudisium', 100)->nullable()->default(null);
            $table->date('tgl_sk_yudisium')->nullable()->default(null);
            $table->decimal('ipk', 3, 2)->nullable()->default(null);
            $table->string('nm_pt_asal', 255)->nullable()->default(null);
            $table->string('nm_prodi_asal', 255)->nullable()->default(null);
            $table->unsignedBigInteger('ro_jns_daftar')->nullable()->default(null)->comment('FK ke reference_option (jns_pendaftaran)');
            $table->unsignedBigInteger('ro_jns_keluar')->nullable()->default(null)->comment('FK ke reference_option (jns_keluar)');
            $table->integer('keluar_smt')->nullable()->default(null);
            $table->text('keterangan')->nullable();
            $table->string('pembiayaan', 100)->nullable()->default(null);
            $table->string('status', 50)->nullable()->default(null);
            $table->unsignedBigInteger('id_tahun_akademik')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('ro_jns_daftar')->references('id')->on('reference_option')->onDelete('set null');
            $table->foreign('ro_jns_keluar')->references('id')->on('reference_option')->onDelete('set null');
            $table->foreign('id_jurusan')->references('id')->on('jurusan')->onDelete('cascade');
            $table->foreign('ro_program_sekolah')->references('id')->on('reference_option')->onDelete('set null');
            $table->foreign('id_siswa_data')->references('id')->on('siswa_data')->onDelete('cascade');
            $table->foreign('ro_status_siswa')->references('id')->on('reference_option')->onDelete('set null');
            $table->foreign('id_wali_dosen')->references('id')->on('dosen_data');
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademik')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('riwayat_pendidikan'); } 
};
