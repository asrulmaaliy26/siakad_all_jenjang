<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->unsignedBigInteger('id_jurusan');
            $table->unsignedBigInteger('id_tahun_akademik');
            $table->enum('status_aktif', ['Y','N'])->nullable()->default('Y');
            $table->timestamps();
            $table->foreign('id_jurusan')->references('id')->on('jurusan')->onDelete('cascade');
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademik')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('kurikulum'); } 
};
