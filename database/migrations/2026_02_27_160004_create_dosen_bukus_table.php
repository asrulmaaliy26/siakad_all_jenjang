<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('dosen_bukus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->nullable()->default(null);
            $table->string('id_staff', 255)->nullable()->default(null);
            $table->string('judul_buku', 255)->nullable()->default(null);
            $table->string('tahun_buku', 10)->nullable()->default(null);
            $table->string('isbn', 255)->nullable()->default(null);
            $table->text('link_isbn')->nullable();
            $table->string('penerbit', 255)->nullable()->default(null);
            $table->timestamp('deleted_at')->nullable()->default(null);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('dosen_bukus'); } 
};
