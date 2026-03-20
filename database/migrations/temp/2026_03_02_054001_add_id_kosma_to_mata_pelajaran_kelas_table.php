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
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kosma')->nullable()->after('id_dosen_data');
            $table->foreign('id_kosma')->references('id')->on('siswa_data')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->dropForeign(['id_kosma']);
            $table->dropColumn('id_kosma');
        });
    }
};
