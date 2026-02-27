<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->unsignedBigInteger('id_fakultas')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('id_fakultas')->references('id')->on('fakultas')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('jurusan'); } 
};
