<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('tahun_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 20)->comment('Contoh: 2024/2025');
            $table->enum('periode', ['Ganjil','Genap']);
            $table->enum('status', ['Y','N'])->nullable()->default('N')->comment('Y = Aktif');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('tahun_akademik'); } 
};
