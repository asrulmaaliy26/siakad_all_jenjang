<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('absensi_siswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_krs');
            $table->unsignedBigInteger('id_mata_pelajaran_kelas');
            $table->enum('status', ['Hadir','Izin','Sakit','Alpa'])->nullable()->default('Hadir');
            $table->dateTime('waktu_absen')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('id_krs')->references('id')->on('akademik_krs')->onDelete('cascade');
            $table->foreign('id_mata_pelajaran_kelas')->references('id')->on('mata_pelajaran_kelas')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('absensi_siswa'); } 
};
