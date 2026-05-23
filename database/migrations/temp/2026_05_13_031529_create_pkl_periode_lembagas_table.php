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
        Schema::create('pkl_periode_lembagas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pkl_periode');
            $table->unsignedBigInteger('id_pkl_lembaga');
            $table->integer('kuota')->default(0);
            $table->timestamps();

            $table->foreign('id_pkl_periode')->references('id')->on('pkl_periodes')->onDelete('cascade');
            $table->foreign('id_pkl_lembaga')->references('id')->on('pkl_lembagas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_periode_lembagas');
    }
};
