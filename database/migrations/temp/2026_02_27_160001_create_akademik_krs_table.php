<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('akademik_krs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_riwayat_pendidikan');
            $table->string('ro_program_kelas', 50)->nullable()->default(null);
            $table->integer('jumlah_sks')->nullable()->default('0');
            $table->date('tgl_krs')->nullable()->default(null);
            $table->string('kode_tahun', 50)->nullable()->default(null);
            $table->enum('status_bayar', ['Y','N'])->nullable()->default('N');
            $table->enum('syarat_uts', ['Y','N'])->nullable()->default('N');
            $table->enum('syarat_uas', ['Y','N'])->nullable()->default('N');
            $table->enum('syarat_krs', ['Y','N'])->nullable()->default('N');
            $table->string('kwitansi_krs', 255)->nullable()->default(null);
            $table->string('berkas_lain', 255)->nullable()->default(null);
            $table->enum('status_aktif', ['Y','N'])->nullable()->default('Y');
            $table->timestamps();
            $table->foreign('id_riwayat_pendidikan')->references('id')->on('riwayat_pendidikan')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('akademik_krs'); } 
};
