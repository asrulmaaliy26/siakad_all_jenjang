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
        // 1. Tambahkan id_staff ke dosen_data
        Schema::table('dosen_data', function (Blueprint $table) {
            if (!Schema::hasColumn('dosen_data', 'id_staff')) {
                $table->string('id_staff', 255)->nullable()->after('id')->index();
            }
        });

        // 2. Tambahkan id_staff ke dosen_dokumen (yang sudah ada)
        Schema::table('dosen_dokumen', function (Blueprint $table) {
            if (!Schema::hasColumn('dosen_dokumen', 'id_staff')) {
                $table->string('id_staff', 255)->nullable()->after('id_dosen')->index();
            }
            if (!Schema::hasColumn('dosen_dokumen', 'lokasi_file')) {
                $table->text('lokasi_file')->nullable()->after('file_path');
            }
        });

        // 3. Buat tabel dosen_bukus (tb_buku)
        Schema::create('dosen_bukus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->index();
            $table->string('id_staff', 255)->nullable()->index(); // Legacy id_staff dari raw DB
            $table->string('judul_buku')->nullable();
            $table->string('tahun_buku', 10)->nullable();
            $table->string('isbn')->nullable();
            $table->text('link_isbn')->nullable();
            $table->string('penerbit')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Buat tabel dosen_penelitians (tb_penelitian)
        Schema::create('dosen_penelitians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->index();
            $table->string('id_staff', 255)->nullable()->index();
            $table->string('judul_penelitian')->nullable();
            $table->string('th_penelitian', 10)->nullable();
            $table->string('dana_penelitian')->nullable();
            $table->string('tingkat_penelitian')->nullable();
            $table->text('lokasi_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Buat tabel dosen_pengabdians (tb_pengabdian)
        Schema::create('dosen_pengabdians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->index();
            $table->string('id_staff', 255)->nullable()->index();
            $table->string('judul_pengabdian')->nullable();
            $table->string('tahun_pengabdian', 10)->nullable();
            $table->string('dana_pengabdian')->nullable();
            $table->string('tingkat_pengabdian')->nullable();
            $table->text('lokasi_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Buat tabel dosen_penghargaans (tb_penghargaan)
        Schema::create('dosen_penghargaans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->index();
            $table->string('id_staff', 255)->nullable()->index();
            $table->string('judul_penghargaan')->nullable();
            $table->string('jenis_penghargaan')->nullable();
            $table->string('tahun_penghargaan', 10)->nullable();
            $table->string('tingkat_penghargaan')->nullable();
            $table->text('lokasi_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Buat tabel dosen_riwayat_pendidikans (tb_riwayat_pendidikan)
        Schema::create('dosen_riwayat_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->index();
            $table->string('id_staff', 255)->nullable()->index();
            $table->string('jenjang')->nullable();
            $table->string('nama_pendidikan')->nullable();
            $table->string('gelar_pendidikan')->nullable();
            $table->string('th_lulus', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_riwayat_pendidikans');
        Schema::dropIfExists('dosen_penghargaans');
        Schema::dropIfExists('dosen_pengabdians');
        Schema::dropIfExists('dosen_penelitians');
        Schema::dropIfExists('dosen_bukus');

        Schema::table('dosen_dokumen', function (Blueprint $table) {
            $table->dropColumn(['id_staff', 'lokasi_file']);
        });

        Schema::table('dosen_data', function (Blueprint $table) {
            $table->dropColumn('id_staff');
        });
    }
};
