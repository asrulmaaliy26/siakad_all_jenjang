<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('mata_pelajaran_master', function (Blueprint $table) {
            $table->id();
            $table->string('kode_feeder', 50)->nullable()->default(null);
            $table->string('nama', 255);
            $table->unsignedBigInteger('id_jurusan');
            $table->integer('bobot')->nullable()->default('3')->comment('SKS');
            $table->enum('jenis', ['wajib','peminatan'])->nullable()->default('wajib');
            $table->timestamps();
            $table->foreign('id_jurusan')->references('id')->on('jurusan')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('mata_pelajaran_master'); } 
};
