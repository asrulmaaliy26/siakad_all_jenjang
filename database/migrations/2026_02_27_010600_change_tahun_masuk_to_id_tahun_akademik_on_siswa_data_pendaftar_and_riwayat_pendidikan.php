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
        Schema::table('siswa_data_pendaftar', function (Blueprint $table) {
            $table->foreignId('id_tahun_akademik')->nullable()->constrained('tahun_akademik')->nullOnDelete();
            if (Schema::hasColumn('siswa_data_pendaftar', 'Tahun_Masuk')) {
                $table->dropColumn('Tahun_Masuk');
            }
        });

        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->foreignId('id_tahun_akademik')->nullable()->constrained('tahun_akademik')->nullOnDelete();
            if (Schema::hasColumn('riwayat_pendidikan', 'th_masuk')) {
                $table->dropColumn('th_masuk');
            }
            if (Schema::hasColumn('riwayat_pendidikan', 'angkatan')) {
                $table->dropColumn('angkatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_data_pendaftar', function (Blueprint $table) {
            $table->dropForeign(['id_tahun_akademik']);
            $table->dropColumn('id_tahun_akademik');
            $table->string('Tahun_Masuk')->nullable();
        });

        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->dropForeign(['id_tahun_akademik']);
            $table->dropColumn('id_tahun_akademik');
            $table->string('th_masuk')->nullable();
            $table->string('angkatan')->nullable();
        });
    }
};
