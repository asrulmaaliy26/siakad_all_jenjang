<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_riwayat_pendidikan');
            $table->unsignedBigInteger('id_tahun_akademik');
            $table->string('jenis_surat', 255);
            $table->text('keperluan');
            $table->string('status', 255)->default('diajukan');
            $table->text('catatan_admin')->nullable();
            $table->string('file_pendukung', 255)->nullable()->default(null);
            $table->string('file_hasil', 255)->nullable()->default(null);
            $table->timestamps();
            $table->foreign('id_riwayat_pendidikan')->references('id')->on('riwayat_pendidikan')->onDelete('cascade');
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademik')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('pengajuan_surats'); } 
};
