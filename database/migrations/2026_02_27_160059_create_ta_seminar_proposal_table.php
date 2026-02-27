<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('ta_seminar_proposal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tahun_akademik');
            $table->unsignedBigInteger('id_riwayat_pendidikan');
            $table->string('judul', 500);
            $table->text('abstrak')->nullable();
            $table->date('tgl_pengajuan');
            $table->date('tgl_ujian')->nullable()->default(null);
            $table->string('ruangan_ujian', 50)->nullable()->default(null);
            $table->date('tgl_acc_judul')->nullable()->default(null);
            $table->string('file', 255)->nullable()->default(null);
            $table->string('file_kwitansi', 255)->nullable()->default(null);
            $table->string('file_surat', 255)->nullable()->default(null);
            $table->string('file_plagiasi', 255)->nullable()->default(null);
            $table->unsignedBigInteger('id_dosen_pembimbing_1')->nullable()->default(null);
            $table->unsignedBigInteger('id_dosen_pembimbing_2')->nullable()->default(null);
            $table->unsignedBigInteger('id_dosen_pembimbing_3')->nullable()->default(null);
            $table->enum('status_dosen_1', ['pending','lulus','tidak_lulus','revisi'])->nullable()->default('pending');
            $table->enum('status_dosen_2', ['pending','lulus','tidak_lulus','revisi'])->nullable()->default('pending');
            $table->enum('status_dosen_3', ['pending','lulus','tidak_lulus','revisi'])->nullable()->default('pending');
            $table->decimal('nilai_dosen_1', 5, 2)->nullable()->default(null);
            $table->decimal('nilai_dosen_2', 5, 2)->nullable()->default(null);
            $table->decimal('nilai_dosen_3', 5, 2)->nullable()->default(null);
            $table->string('file_revisi_1', 255)->nullable()->default(null);
            $table->string('file_revisi_2', 255)->nullable()->default(null);
            $table->string('file_revisi_3', 255)->nullable()->default(null);
            $table->text('ctt_revisi_dosen_1')->nullable();
            $table->text('ctt_revisi_dosen_2')->nullable();
            $table->text('ctt_revisi_dosen_3')->nullable();
            $table->enum('status', ['pending','disetujui','ditolak','revisi','selesai'])->nullable()->default('pending');
            $table->timestamps();
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademik');
            $table->foreign('id_riwayat_pendidikan')->references('id')->on('riwayat_pendidikan');
            $table->foreign('id_dosen_pembimbing_1')->references('id')->on('dosen_data');
            $table->foreign('id_dosen_pembimbing_2')->references('id')->on('dosen_data');
            $table->foreign('id_dosen_pembimbing_3')->references('id')->on('dosen_data');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('ta_seminar_proposal'); } 
};
