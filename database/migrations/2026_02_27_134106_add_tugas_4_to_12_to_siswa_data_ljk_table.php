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
        Schema::table('siswa_data_ljk', function (Blueprint $table) {
            for ($i = 4; $i <= 12; $i++) {
                $table->json("ljk_tugas_{$i}")->nullable();
                $table->text("ctt_tugas_{$i}")->nullable();
                $table->decimal("Nilai_TGS_{$i}", 5, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_data_ljk', function (Blueprint $table) {
            for ($i = 4; $i <= 12; $i++) {
                $table->dropColumn([
                    "ljk_tugas_{$i}",
                    "ctt_tugas_{$i}",
                    "Nilai_TGS_{$i}",
                ]);
            }
        });
    }
};
