<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status to string in sarpras_peminjamen to avoid ENUM truncation warnings
        DB::statement("ALTER TABLE sarpras_peminjamen MODIFY COLUMN status VARCHAR(50) DEFAULT 'Diajukan'");
        
        // Change status to string in sarpras_surat_keluars
        DB::statement("ALTER TABLE sarpras_surat_keluars MODIFY COLUMN status VARCHAR(50) DEFAULT 'Draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE sarpras_peminjamen MODIFY COLUMN status ENUM('Diajukan', 'Disetujui', 'Ditolak', 'Dipinjam', 'Dikembalikan', 'Telat') DEFAULT 'Diajukan'");
        DB::statement("ALTER TABLE sarpras_surat_keluars MODIFY COLUMN status ENUM('Draft', 'Sent', 'Archived') DEFAULT 'Draft'");
    }
};
