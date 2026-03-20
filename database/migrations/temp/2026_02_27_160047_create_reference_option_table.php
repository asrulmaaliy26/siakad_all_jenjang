<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('reference_option', function (Blueprint $table) {
            $table->id();
            $table->string('nama_grup', 100)->comment('Nama grup option (program_kelas, ruang_kelas, status_siswa, dll)');
            $table->string('kode', 50)->nullable()->default(null)->comment('Kode singkat');
            $table->string('nilai', 255)->comment('Nilai/Label yang ditampilkan');
            $table->enum('status', ['Y','N'])->nullable()->default('Y')->comment('Status aktif');
            $table->text('deskripsi')->nullable()->comment('Deskripsi tambahan');
            $table->enum('is_aktif', ['Y','N'])->nullable()->default(null);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('reference_option'); } 
};
