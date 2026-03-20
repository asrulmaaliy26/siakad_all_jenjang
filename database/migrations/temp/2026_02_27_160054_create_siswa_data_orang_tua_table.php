<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('siswa_data_orang_tua', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa_data');
            $table->string('Nama_Ayah', 255)->nullable()->default(null);
            $table->string('Tempat_Lhr_Ayah', 255)->nullable()->default(null);
            $table->string('Tgl_Lhr_Ayah', 255)->nullable()->default(null);
            $table->string('Bln_Lhr_Ayah', 255)->nullable()->default(null);
            $table->string('Thn_Lhr_ayah', 255)->nullable()->default(null);
            $table->string('Agama_Ayah', 255)->nullable()->default(null);
            $table->string('Gol_Darah_Ayah', 255)->nullable()->default(null);
            $table->string('Pendidikan_Terakhir_Ayah', 255)->nullable()->default(null);
            $table->string('Pekerjaan_Ayah', 255)->nullable()->default(null);
            $table->string('Penghasilan_Ayah', 255)->nullable()->default(null);
            $table->string('Kebutuhan_Khusus_Ayah', 255)->nullable()->default(null);
            $table->string('Nomor_KTP_Ayah', 255)->nullable()->default(null);
            $table->string('Alamat_Ayah', 255)->nullable()->default(null);
            $table->string('No_Rmh_Ayah', 255)->nullable()->default(null);
            $table->string('Dusun_Ayah', 255)->nullable()->default(null);
            $table->string('RT_Ayah', 255)->nullable()->default(null);
            $table->string('RW_Ayah', 255)->nullable()->default(null);
            $table->string('Desa_Ayah', 255)->nullable()->default(null);
            $table->string('Kec_Ayah', 255)->nullable()->default(null);
            $table->string('Kab_Ayah', 255)->nullable()->default(null);
            $table->string('Kode_Pos_Ayah', 255)->nullable()->default(null);
            $table->string('Prov_Ayah', 255)->nullable()->default(null);
            $table->string('Kewarganegaraan_Ayah', 255)->nullable()->default(null);
            $table->string('Nama_Ibu', 255)->nullable()->default(null);
            $table->string('Tempat_Lhr_Ibu', 255)->nullable()->default(null);
            $table->string('Tgl_Lhr_Ibu', 255)->nullable()->default(null);
            $table->string('Bln_Lhr_Ibu', 255)->nullable()->default(null);
            $table->string('Thn_Lhr_Ibu', 255)->nullable()->default(null);
            $table->string('Agama_Ibu', 255)->nullable()->default(null);
            $table->string('Gol_Darah_Ibu', 255)->nullable()->default(null);
            $table->string('Pendidikan_Terakhir_Ibu', 255)->nullable()->default(null);
            $table->string('Pekerjaan_Ibu', 255)->nullable()->default(null);
            $table->string('Penghasilan_Ibu', 255)->nullable()->default(null);
            $table->string('Kebutuhan_Khusus_Ibu', 255)->nullable()->default(null);
            $table->string('Nomor_KTP_Ibu', 255)->nullable()->default(null);
            $table->string('Alamat_Ibu', 255)->nullable()->default(null);
            $table->string('No_Rmh_Ibu', 255)->nullable()->default(null);
            $table->string('Dusun_Ibu', 255)->nullable()->default(null);
            $table->string('RT_Ibu', 255)->nullable()->default(null);
            $table->string('RW_Ibu', 255)->nullable()->default(null);
            $table->string('Desa_Ibu', 255)->nullable()->default(null);
            $table->string('Kec_Ibu', 255)->nullable()->default(null);
            $table->string('Kab_Ibu', 255)->nullable()->default(null);
            $table->string('Kode_Pos_Ibu', 255)->nullable()->default(null);
            $table->string('Prov_Ibu', 255)->nullable()->default(null);
            $table->string('Kewarganegaraan_Ibu', 255)->nullable()->default(null);
            $table->string('No_HP_ayah', 16)->nullable()->default(null);
            $table->string('No_HP_ibu', 16)->nullable()->default(null);
            $table->timestamps();
            $table->foreign('id_siswa_data')->references('id')->on('siswa_data')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('siswa_data_orang_tua'); } 
};
