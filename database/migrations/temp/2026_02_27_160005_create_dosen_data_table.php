<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('dosen_data', function (Blueprint $table) {
            $table->id();
            $table->string('id_staff', 255)->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->string('foto_profil', 255)->nullable()->default(null);
            $table->string('nama', 255);
            $table->string('NIPDN', 50)->nullable()->default(null);
            $table->string('NIY', 50)->nullable()->default(null);
            $table->string('gelar_depan', 50)->nullable()->default(null);
            $table->string('gelar_belakang', 50)->nullable()->default(null);
            $table->unsignedBigInteger('ro_pangkat_gol')->nullable()->default(null)->comment('FK ke reference_option (pangkat)');
            $table->unsignedBigInteger('ro_jabatan')->nullable()->default(null)->comment('FK ke reference_option (jabatan_fungsional)');
            $table->unsignedBigInteger('id_jurusan')->nullable()->default(null);
            $table->string('email', 255)->nullable()->default(null);
            $table->date('tanggal_lahir')->nullable()->default(null);
            $table->enum('jenis_kelamin', ['L','P'])->nullable()->default(null);
            $table->string('ibu_kandung', 255)->nullable()->default(null);
            $table->string('kewarganegaraan', 100)->nullable()->default(null);
            $table->text('Alamat')->nullable();
            $table->enum('status_kawin', ['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'])->nullable()->default(null);
            $table->unsignedBigInteger('ro_status_dosen')->nullable()->default(null)->comment('FK ke reference_option (status_dosen)');
            $table->unsignedBigInteger('ro_agama')->nullable()->default(null)->comment('FK ke reference_option (agama)');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ro_agama')->references('id')->on('reference_option')->onDelete('set null');
            $table->foreign('ro_jabatan')->references('id')->on('reference_option')->onDelete('set null');
            $table->foreign('id_jurusan')->references('id')->on('jurusan')->onDelete('set null');
            $table->foreign('ro_pangkat_gol')->references('id')->on('reference_option')->onDelete('set null');
            $table->foreign('ro_status_dosen')->references('id')->on('reference_option')->onDelete('set null');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('dosen_data'); } 
};
