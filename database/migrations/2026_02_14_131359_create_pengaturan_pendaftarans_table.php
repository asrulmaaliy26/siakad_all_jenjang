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
        Schema::create('pengaturan_pendaftaran', function (Blueprint $table) {
            $table->id();

            // Pengaturan Biaya
            $table->decimal('biaya_reguler', 15, 2)->default(100000);
            $table->decimal('biaya_beasiswa', 15, 2)->default(50000);

            // Pengaturan Visual
            $table->string('foto_header')->nullable();
            $table->string('foto_banner')->nullable();
            $table->text('deskripsi_pendaftaran')->nullable();

            // Pengaturan Akses
            $table->boolean('status_pendaftaran')->default(true); // true = buka, false = tutup
            $table->dateTime('tanggal_buka')->nullable();
            $table->dateTime('tanggal_tutup')->nullable();

            // Pengaturan Tambahan
            $table->foreignId('id_tahun_akademik')->nullable()->constrained('tahun_akademik')->onDelete('set null');
            $table->text('pengumuman')->nullable();
            $table->string('kontak_admin')->nullable();
            $table->string('email_admin')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pendaftaran');
    }
};
