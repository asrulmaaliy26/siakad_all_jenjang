<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('siswa_data_ljk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_akademik_krs');
            $table->unsignedBigInteger('id_mata_pelajaran_kelas');
            $table->decimal('nilai', 5, 2)->nullable()->default(null);
            $table->string('ljk_simulasi', 255)->nullable()->default(null);
            $table->text('ljk_uas')->nullable();
            $table->text('artikel_uas')->nullable();
            $table->string('tgl_upload_ljk_uas', 50)->nullable()->default(null);
            $table->string('tgl_upload_artikel_uas', 50)->nullable()->default(null);
            $table->text('ljk_uts')->nullable();
            $table->text('artikel_uts')->nullable();
            $table->string('tgl_upload_ljk_uts', 50)->nullable()->default(null);
            $table->string('tgl_upload_artikel_uts', 50)->nullable()->default(null);
            $table->text('tugas')->nullable();
            $table->text('ljk_tugas_1')->nullable();
            $table->text('ljk_tugas_2')->nullable();
            $table->text('ljk_tugas_3')->nullable();
            $table->string('tgl_upload_tugas', 50)->nullable()->default(null);
            $table->string('Nilai_UTS', 255)->nullable()->default(null);
            $table->string('Nilai_TGS_1', 255)->nullable()->default(null);
            $table->string('Nilai_TGS_2', 255)->nullable()->default(null);
            $table->string('Nilai_TGS_3', 255)->nullable()->default(null);
            $table->string('Nilai_UAS', 255)->nullable()->default(null);
            $table->string('Nilai_Performance', 255)->nullable()->default(null);
            $table->string('Nilai_Akhir', 255)->nullable()->default(null);
            $table->string('Nilai_Huruf', 255)->nullable()->default(null);
            $table->enum('Status_Nilai', ['L','TL'])->nullable()->default('TL');
            $table->string('Rekom_Nilai', 255)->nullable()->default(null);
            $table->string('ket', 5)->nullable()->default(null);
            $table->enum('transfer', ['Y','N'])->nullable()->default(null);
            $table->enum('cekal_kuliah', ['Y','N'])->nullable()->default('N');
            $table->text('ctt_uts')->nullable();
            $table->text('ctt_uas')->nullable();
            $table->text('ctt_tugas_1')->nullable();
            $table->text('ctt_tugas_2')->nullable();
            $table->text('ctt_tugas_3')->nullable();
            $table->json('ljk_tugas_4')->nullable()->default(null);
            $table->text('ctt_tugas_4')->nullable();
            $table->decimal('Nilai_TGS_4', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_5')->nullable()->default(null);
            $table->text('ctt_tugas_5')->nullable();
            $table->decimal('Nilai_TGS_5', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_6')->nullable()->default(null);
            $table->text('ctt_tugas_6')->nullable();
            $table->decimal('Nilai_TGS_6', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_7')->nullable()->default(null);
            $table->text('ctt_tugas_7')->nullable();
            $table->decimal('Nilai_TGS_7', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_8')->nullable()->default(null);
            $table->text('ctt_tugas_8')->nullable();
            $table->decimal('Nilai_TGS_8', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_9')->nullable()->default(null);
            $table->text('ctt_tugas_9')->nullable();
            $table->decimal('Nilai_TGS_9', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_10')->nullable()->default(null);
            $table->text('ctt_tugas_10')->nullable();
            $table->decimal('Nilai_TGS_10', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_11')->nullable()->default(null);
            $table->text('ctt_tugas_11')->nullable();
            $table->decimal('Nilai_TGS_11', 5, 2)->nullable()->default(null);
            $table->json('ljk_tugas_12')->nullable()->default(null);
            $table->text('ctt_tugas_12')->nullable();
            $table->decimal('Nilai_TGS_12', 5, 2)->nullable()->default(null);
            $table->timestamps();
            $table->foreign('id_akademik_krs')->references('id')->on('akademik_krs')->onDelete('cascade');
            $table->foreign('id_mata_pelajaran_kelas')->references('id')->on('mata_pelajaran_kelas')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('siswa_data_ljk'); } 
};
