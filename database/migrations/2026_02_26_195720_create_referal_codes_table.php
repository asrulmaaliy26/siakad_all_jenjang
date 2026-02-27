<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referal_codes', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->text('keterangan')->nullable();
            $table->enum('type', ['internal', 'eksternal'])->default('internal');
            $table->string('status')->nullable(); // menyimpan role/status seperti Mahasiswa, Dosen, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referal_codes');
    }
};
