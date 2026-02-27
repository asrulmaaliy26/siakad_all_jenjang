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
            $table->foreignId('id_referal_code')->nullable()->constrained('referal_codes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_data_pendaftar', function (Blueprint $table) {
            $table->dropForeign(['id_referal_code']);
            $table->dropColumn('id_referal_code');
        });
    }
};
