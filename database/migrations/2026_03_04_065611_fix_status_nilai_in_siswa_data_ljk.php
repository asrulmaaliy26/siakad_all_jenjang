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
        Schema::table('siswa_data_ljk', function (Blueprint $table) {
            $table->string('Status_Nilai', 20)->nullable()->change();
            $table->text('ket')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_data_ljk', function (Blueprint $table) {
            $table->enum('Status_Nilai', ['L', 'TL'])->nullable()->change();
            $table->string('ket', 5)->nullable()->change();
        });
    }
};
