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
        Schema::create('sarpras_peminjamen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sarpras_barang_id')->constrained('sarpras_barangs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('jumlah_pinjam')->default(1);
            $table->dateTime('tanggal_pinjam');
            $table->dateTime('estimasi_kembali');
            $table->dateTime('tanggal_kembali')->nullable();
            $table->enum('status', ['Dipinjam', 'Dikembalikan', 'Telat'])->default('Dipinjam');
            $table->text('keterangan')->nullable();
            $table->foreignId('sarpras_surat_keluar_id')->nullable()->constrained('sarpras_surat_keluars')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpras_peminjamen');
    }
};
