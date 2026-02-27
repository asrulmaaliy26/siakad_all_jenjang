<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wisuda_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_riwayat_pendidikan')->constrained('riwayat_pendidikan')->onDelete('cascade');

            // Clearances (Bebas Tanggungan)
            $table->boolean('bebas_prodi')->default(false);
            $table->boolean('bebas_fakultas')->default(false);
            $table->boolean('bebas_perpustakaan')->default(false);
            $table->boolean('bebas_keuangan')->default(false);

            // Form Registration Data
            $table->string('nama_arab')->nullable();
            $table->string('tempat_lahir_arab')->nullable();
            $table->text('alamat_malang')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('pas_foto')->nullable();

            // Advisors (referenced from DosenData)
            $table->foreignId('id_pembimbing_1')->nullable()->constrained('dosen_data');
            $table->foreignId('id_pembimbing_2')->nullable()->constrained('dosen_data');

            // Period
            $table->foreignId('id_periode_wisuda')->nullable()->constrained('periode_wisudas');

            $table->enum('status_pendaftaran', ['Proses', 'Disetujui', 'Ditolak'])->default('Proses');
            $table->timestamp('tanggal_daftar')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisuda_mahasiswas');
    }
};
