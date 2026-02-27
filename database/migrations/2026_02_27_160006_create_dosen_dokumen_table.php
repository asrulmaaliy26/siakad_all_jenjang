<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('dosen_dokumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen');
            $table->string('id_staff', 255)->nullable()->default(null);
            $table->string('judul_dokumen', 255);
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->text('lokasi_file')->nullable();
            $table->integer('file_size')->nullable()->default(null);
            $table->string('file_type', 100)->nullable()->default(null);
            $table->enum('tipe_dokumen', ['materi','tugas','rpp','silabus','lainnya'])->nullable()->default('lainnya');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_public')->nullable()->default('0');
            $table->timestamps();
            $table->foreign('id_dosen')->references('id')->on('dosen_data')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('dosen_dokumen'); } 
};
