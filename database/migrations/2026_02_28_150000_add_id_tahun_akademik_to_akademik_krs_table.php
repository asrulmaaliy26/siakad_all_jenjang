<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('akademik_krs', function (Blueprint $table) {
            // Tambah kolom FK baru
            $table->unsignedBigInteger('id_tahun_akademik')
                ->nullable()
                ->default(null)
                ->after('tgl_krs')
                ->comment('FK ke tahun_akademik.id (menggantikan kode_tahun)');
        });

        // Migrasi data: isi id_tahun_akademik dari kode_tahun (match by nama)
        DB::statement("
            UPDATE akademik_krs ak
            JOIN tahun_akademik ta ON ta.nama = ak.kode_tahun
            SET ak.id_tahun_akademik = ta.id
            WHERE ak.kode_tahun IS NOT NULL AND ak.id_tahun_akademik IS NULL
        ");

        Schema::table('akademik_krs', function (Blueprint $table) {
            $table->foreign('id_tahun_akademik')
                ->references('id')
                ->on('tahun_akademik')
                ->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('akademik_krs', function (Blueprint $table) {
            $table->dropForeign(['id_tahun_akademik']);
            $table->dropColumn('id_tahun_akademik');
        });
    }
};
