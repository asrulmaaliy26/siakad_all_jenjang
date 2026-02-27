<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('pekan_ujian', function (Blueprint $table) {
            $table->integer('id');
            $table->unsignedBigInteger('id_tahun_akademik');
            $table->enum('jenis_ujian', ['UTS','UAS'])->nullable()->default(null);
            $table->enum('status_akses', ['Y','N'])->nullable()->default(null);
            $table->enum('status_bayar', ['Y','N'])->nullable()->default(null);
            $table->enum('status_ujian', ['Y','N'])->nullable()->default(null);
            $table->text('informasi')->nullable();
            $table->timestamps();
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademik');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('pekan_ujian'); } 
};
