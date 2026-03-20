<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('dosen_jurnal_pengajaran', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->enum('type', ['Tugas','Materi'])->nullable()->default('Materi');
            $table->unsignedBigInteger('id_mata_pelajaran_kelas');
            $table->text('description')->nullable();
            $table->date('deadline')->nullable()->default(null);
            $table->enum('status_akses', ['Y','N'])->nullable()->default('N');
            $table->timestamps();
            $table->foreign('id_mata_pelajaran_kelas')->references('id')->on('mata_pelajaran_kelas')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('dosen_jurnal_pengajaran'); } 
};
