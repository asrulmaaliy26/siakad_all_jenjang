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
        Schema::create('sarpras_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('merek')->nullable();
            $table->integer('jumlah')->default(0);
            $table->foreignId('sarpras_kategori_id')->constrained('sarpras_kategoris')->cascadeOnDelete();
            $table->foreignId('id_jurusan')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status_penggunaan', ['Tersedia', 'Digunakan', 'Dipinjam', 'Dihapus'])->default('Tersedia');
            $table->date('tanggal_pengadaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->json('lampiran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpras_barangs');
    }
};
