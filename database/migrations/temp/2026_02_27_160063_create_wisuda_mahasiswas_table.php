<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('wisuda_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_riwayat_pendidikan');
            $table->boolean('bebas_prodi')->default('0');
            $table->boolean('bebas_fakultas')->default('0');
            $table->boolean('bebas_perpustakaan')->default('0');
            $table->boolean('bebas_keuangan')->default('0');
            $table->string('nama_arab', 255)->nullable()->default(null);
            $table->string('tempat_lahir_arab', 255)->nullable()->default(null);
            $table->text('alamat_malang')->nullable();
            $table->string('no_hp', 255)->nullable()->default(null);
            $table->string('email', 255)->nullable()->default(null);
            $table->string('pas_foto', 255)->nullable()->default(null);
            $table->unsignedBigInteger('id_pembimbing_1')->nullable()->default(null);
            $table->unsignedBigInteger('id_pembimbing_2')->nullable()->default(null);
            $table->unsignedBigInteger('id_periode_wisuda')->nullable()->default(null);
            $table->enum('status_pendaftaran', ['Proses','Disetujui','Ditolak'])->default('Proses');
            $table->timestamp('tanggal_daftar');
            $table->timestamps();
            $table->foreign('id_pembimbing_1')->references('id')->on('dosen_data');
            $table->foreign('id_pembimbing_2')->references('id')->on('dosen_data');
            $table->foreign('id_periode_wisuda')->references('id')->on('periode_wisudas');
            $table->foreign('id_riwayat_pendidikan')->references('id')->on('riwayat_pendidikan')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('wisuda_mahasiswas'); } 
};
