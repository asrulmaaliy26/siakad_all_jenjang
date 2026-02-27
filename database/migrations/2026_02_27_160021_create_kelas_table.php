<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ro_program_kelas')->comment('FK ke reference_option (program_kelas)');
            $table->integer('semester')->default('1');
            $table->integer('total')->nullable()->default('20');
            $table->unsignedBigInteger('id_tahun_akademik');
            $table->unsignedBigInteger('id_jurusan')->nullable()->default(null);
            $table->enum('status_aktif', ['Y','N'])->nullable()->default('Y');
            $table->timestamps();
            $table->foreign('id_jurusan')->references('id')->on('jurusan')->onDelete('set null');
            $table->foreign('ro_program_kelas')->references('id')->on('reference_option')->onDelete('cascade');
            $table->foreign('id_tahun_akademik')->references('id')->on('tahun_akademik')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('kelas'); } 
};
