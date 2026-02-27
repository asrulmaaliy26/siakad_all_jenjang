<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('mata_pelajaran_kurikulum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kurikulum');
            $table->unsignedBigInteger('id_mata_pelajaran_master');
            $table->integer('semester')->default('1');
            $table->timestamps();
            $table->foreign('id_kurikulum')->references('id')->on('kurikulum')->onDelete('cascade');
            $table->foreign('id_mata_pelajaran_master')->references('id')->on('mata_pelajaran_master')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('mata_pelajaran_kurikulum'); } 
};
