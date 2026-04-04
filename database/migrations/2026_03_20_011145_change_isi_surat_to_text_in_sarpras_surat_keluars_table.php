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
            $table->text('isi_surat')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sarpras_surat_keluars', function (Blueprint $table) {
            $table->json('isi_surat')->nullable()->change();
        });
    }
};
