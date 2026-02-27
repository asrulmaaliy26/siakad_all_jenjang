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
            $table->renameColumn('Status_Kelulusan', 'Status_Kelulusan_Seleksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_data_pendaftar', function (Blueprint $table) {
            $table->renameColumn('Status_Kelulusan_Seleksi', 'Status_Kelulusan');
        });
    }
};
