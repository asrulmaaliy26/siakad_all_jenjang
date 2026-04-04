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
        // Using raw SQL to ensure enum update works correctly across different environments
        DB::statement("ALTER TABLE sarpras_peminjamen MODIFY COLUMN status ENUM('Diajukan', 'Dipinjam', 'Dikembalikan', 'Telat') DEFAULT 'Diajukan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE sarpras_peminjamen MODIFY COLUMN status ENUM('Dipinjam', 'Dikembalikan', 'Telat') DEFAULT 'Dipinjam'");
    }
};
