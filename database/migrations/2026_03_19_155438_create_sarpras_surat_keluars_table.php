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
        Schema::create('sarpras_surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sarpras_surat_kategori_id')->constrained('sarpras_surat_kategoris')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nomor_surat')->unique();
            $table->string('perihal');
            $table->string('tujuan');
            $table->date('tanggal_surat');
            $table->json('isi_surat')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['Draft', 'Sent', 'Archived'])->default('Draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpras_surat_keluars');
    }
};
