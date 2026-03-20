<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mata_pelajaran_kurikulum');
            $table->unsignedBigInteger('id_kelas');
            $table->unsignedBigInteger('id_dosen_data')->nullable()->default(null);
            $table->string('uts', 50)->nullable()->default(null);
            $table->string('uas', 50)->nullable()->default(null);
            $table->unsignedBigInteger('ro_ruang_kelas')->nullable()->default(null)->comment('FK ke reference_option (ruang_kelas)');
            $table->unsignedBigInteger('ro_pelaksanaan_kelas')->nullable()->default(null);
            $table->unsignedBigInteger('id_pengawas')->nullable()->default(null);
            $table->integer('jumlah')->nullable()->default('0');
            $table->string('hari', 50)->nullable()->default(null);
            $table->string('tanggal', 50)->nullable()->default(null);
            $table->text('soal_uas')->nullable();
            $table->text('soal_uts')->nullable();
            $table->string('jam', 50)->nullable()->default(null);
            $table->enum('status_uts', ['Y','N'])->nullable()->default('N');
            $table->enum('status_uas', ['Y','N'])->nullable()->default('N');
            $table->string('ruang_uts', 100)->nullable()->default(null);
            $table->string('ruang_uas', 100)->nullable()->default(null);
            $table->text('link_kelas')->nullable();
            $table->string('passcode', 100)->nullable()->default(null);
            $table->text('ctt_soal_uts')->nullable();
            $table->text('ctt_soal_uas')->nullable();
            $table->timestamps();
            $table->foreign('id_dosen_data')->references('id')->on('dosen_data')->onDelete('set null');
            $table->foreign('id_kelas')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('id_mata_pelajaran_kurikulum')->references('id')->on('mata_pelajaran_kurikulum')->onDelete('cascade');
            $table->foreign('ro_ruang_kelas')->references('id')->on('reference_option')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('mata_pelajaran_kelas'); } 
};
