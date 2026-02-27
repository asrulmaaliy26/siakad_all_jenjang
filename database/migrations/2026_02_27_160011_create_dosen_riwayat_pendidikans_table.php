<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('dosen_riwayat_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->default(null);
            $table->string('id_staff', 255)->nullable()->default(null);
            $table->string('jenjang', 255)->nullable()->default(null);
            $table->string('nama_pendidikan', 255)->nullable()->default(null);
            $table->string('gelar_pendidikan', 255)->nullable()->default(null);
            $table->string('th_lulus', 10)->nullable()->default(null);
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('dosen_riwayat_pendidikans'); } 
};
