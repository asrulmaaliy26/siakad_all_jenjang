<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('dosen_pengabdians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->default(null);
            $table->string('id_staff', 255)->nullable()->default(null);
            $table->string('judul_pengabdian', 255)->nullable()->default(null);
            $table->string('tahun_pengabdian', 10)->nullable()->default(null);
            $table->string('dana_pengabdian', 255)->nullable()->default(null);
            $table->string('tingkat_pengabdian', 255)->nullable()->default(null);
            $table->text('lokasi_file')->nullable();
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('dosen_pengabdians'); } 
};
