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
        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->unsignedBigInteger('ro_program_kelas')
                ->nullable()
                ->default(null)
                ->comment('FK ke reference_option (program_kelas)')
                ->after('ro_program_sekolah');

            $table->foreign('ro_program_kelas')
                ->references('id')
                ->on('reference_option')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_pendidikan', function (Blueprint $table) {
            $table->dropForeign(['ro_program_kelas']);
            $table->dropColumn('ro_program_kelas');
        });
    }
};
