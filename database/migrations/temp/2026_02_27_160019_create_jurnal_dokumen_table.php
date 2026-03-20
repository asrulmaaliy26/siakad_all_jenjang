<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('jurnal_dokumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jurnal');
            $table->unsignedBigInteger('id_dokumen');
            $table->timestamps();
            $table->foreign('id_dokumen')->references('id')->on('dosen_dokumen')->onDelete('cascade');
            $table->foreign('id_jurnal')->references('id')->on('dosen_jurnal_pengajaran')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('jurnal_dokumen'); } 
};
