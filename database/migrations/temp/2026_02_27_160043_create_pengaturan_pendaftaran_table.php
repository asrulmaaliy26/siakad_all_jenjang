<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('pengaturan_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->decimal('biaya_reguler', 15, 2)->default('100000.00');
            $table->decimal('biaya_beasiswa', 15, 2)->default('50000.00');
            $table->string('foto_header', 255)->nullable()->default(null);
            $table->string('foto_banner', 255)->nullable()->default(null);
            $table->text('deskripsi_pendaftaran')->nullable();
            $table->boolean('status_pendaftaran')->default('1');
            $table->dateTime('tanggal_buka')->nullable()->default(null);
            $table->dateTime('tanggal_tutup')->nullable()->default(null);
            $table->string('id_tahun_akademik', 50)->nullable()->default(null);
            $table->text('pengumuman')->nullable();
            $table->string('kontak_admin', 255)->nullable()->default(null);
            $table->string('email_admin', 255)->nullable()->default(null);
            $table->string('brosur_pendaftaran', 255)->nullable()->default(null);
            $table->date('gelombang_1_buka')->nullable()->default(null);
            $table->date('gelombang_1_tutup')->nullable()->default(null);
            $table->boolean('gelombang_1_aktif')->default('0');
            $table->date('gelombang_2_buka')->nullable()->default(null);
            $table->date('gelombang_2_tutup')->nullable()->default(null);
            $table->boolean('gelombang_2_aktif')->default('0');
            $table->date('gelombang_3_buka')->nullable()->default(null);
            $table->date('gelombang_3_tutup')->nullable()->default(null);
            $table->boolean('gelombang_3_aktif')->default('0');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('pengaturan_pendaftaran'); } 
};
