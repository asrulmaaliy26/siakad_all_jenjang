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
        Schema::table('sarpras_surat_keluars', function (Blueprint $table) {
            $table->unsignedBigInteger('tahun_akademik_id')->nullable()->after('user_id');
            $table->foreign('tahun_akademik_id')->references('id')->on('tahun_akademik')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sarpras_surat_keluars', function (Blueprint $table) {
            $table->dropForeign(['tahun_akademik_id']);
            $table->dropColumn('tahun_akademik_id');
        });
    }
};
