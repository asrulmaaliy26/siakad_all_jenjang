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
        Schema::table('pengaturan_pendaftaran', function (Blueprint $table) {
            $table->string('brosur_pendaftaran')->nullable();

            $table->date('gelombang_1_buka')->nullable();
            $table->date('gelombang_1_tutup')->nullable();
            $table->boolean('gelombang_1_aktif')->default(false);

            $table->date('gelombang_2_buka')->nullable();
            $table->date('gelombang_2_tutup')->nullable();
            $table->boolean('gelombang_2_aktif')->default(false);

            $table->date('gelombang_3_buka')->nullable();
            $table->date('gelombang_3_tutup')->nullable();
            $table->boolean('gelombang_3_aktif')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_pendaftaran', function (Blueprint $table) {
            $table->dropColumn([
                'brosur_pendaftaran',
                'gelombang_1_buka',
                'gelombang_1_tutup',
                'gelombang_1_aktif',
                'gelombang_2_buka',
                'gelombang_2_tutup',
                'gelombang_2_aktif',
                'gelombang_3_buka',
                'gelombang_3_tutup',
                'gelombang_3_aktif'
            ]);
        });
    }
};
