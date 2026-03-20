<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('siswa_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->string('nama', 255);
            $table->string('nama_lengkap', 255)->nullable()->default(null);
            $table->string('foto_profil', 255)->nullable()->default(null);
            $table->string('nomor_induk', 50)->nullable()->default(null)->comment('NIM/NISN');
            $table->enum('jenis_kelamin', ['L','P'])->nullable()->default(null);
            $table->string('golongan_darah', 5)->nullable()->default(null);
            $table->string('kota_lahir', 100)->nullable()->default(null);
            $table->date('tanggal_lahir')->nullable()->default(null);
            $table->text('alamat')->nullable();
            $table->string('nomor_rumah', 50)->nullable()->default(null);
            $table->string('dusun', 100)->nullable()->default(null);
            $table->string('rt', 10)->nullable()->default(null);
            $table->string('rw', 10)->nullable()->default(null);
            $table->string('desa', 100)->nullable()->default(null);
            $table->string('kecamatan', 100)->nullable()->default(null);
            $table->string('kabupaten', 100)->nullable()->default(null);
            $table->string('kode_pos', 10)->nullable()->default(null);
            $table->string('provinsi', 100)->nullable()->default(null);
            $table->string('tempat_domisili', 255)->nullable()->default(null);
            $table->string('jenis_domisili', 100)->nullable()->default(null);
            $table->string('no_telepon', 20)->nullable()->default(null);
            $table->string('no_ktp', 20)->nullable()->default(null);
            $table->string('no_kk', 20)->nullable()->default(null);
            $table->string('agama', 50)->nullable()->default(null)->comment('Bisa jadi ro_ jika perlu');
            $table->string('kewarganegaraan', 50)->nullable()->default('Indonesia');
            $table->string('kode_negara', 10)->nullable()->default(null);
            $table->string('status_pkawin', 50)->nullable()->default(null);
            $table->string('pekerjaan', 100)->nullable()->default(null);
            $table->string('biaya_ditanggung', 100)->nullable()->default(null);
            $table->string('transportasi', 100)->nullable()->default(null);
            $table->string('status_asal_sekolah', 100)->nullable()->default(null);
            $table->string('asal_slta', 255)->nullable()->default(null);
            $table->string('jenis_slta', 100)->nullable()->default(null);
            $table->string('kejuruan_slta', 100)->nullable()->default(null);
            $table->text('alamat_lengkap_sekolah_asal')->nullable();
            $table->string('tahun_lulus_slta')->nullable()->default(null);
            $table->string('nomor_seri_ijazah_slta', 100)->nullable()->default(null);
            $table->string('nisn', 20)->nullable()->default(null);
            $table->integer('anak_ke')->nullable()->default(null);
            $table->integer('jumlah_saudara')->nullable()->default(null);
            $table->string('email', 255)->nullable()->default(null);
            $table->enum('penerima_kps', ['Y','N'])->nullable()->default('N');
            $table->string('no_kps', 50)->nullable()->default(null);
            $table->text('kebutuhan_khusus')->nullable();
            $table->enum('status_siswa', ['aktif','tidak aktif'])->nullable()->default('tidak aktif');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('siswa_data'); } 
};
