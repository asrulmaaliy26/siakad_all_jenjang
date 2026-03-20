<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('library_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('riwayat_pendidikan_id');
            $table->dateTime('visited_at');
            $table->string('purpose', 255)->nullable()->default(null);
            $table->timestamps();
            $table->foreign('riwayat_pendidikan_id')->references('id')->on('riwayat_pendidikan')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('library_visits'); } 
};
