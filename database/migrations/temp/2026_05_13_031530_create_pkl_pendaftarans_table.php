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
        Schema::create('pkl_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pkl_periode');
            $table->unsignedBigInteger('id_pkl_lembaga');
            $table->unsignedBigInteger('id_siswa_data');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamp('tgl_daftar')->useCurrent();
            $table->timestamps();

            $table->foreign('id_pkl_periode')->references('id')->on('pkl_periodes')->onDelete('cascade');
            $table->foreign('id_pkl_lembaga')->references('id')->on('pkl_lembagas')->onDelete('cascade');
            $table->foreign('id_siswa_data')->references('id')->on('siswa_data')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_pendaftarans');
    }
};
