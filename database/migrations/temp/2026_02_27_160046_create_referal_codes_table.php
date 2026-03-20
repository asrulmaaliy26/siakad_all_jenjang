<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('referal_codes', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('kode', 255);
            $table->text('keterangan')->nullable();
            $table->enum('type', ['internal','eksternal'])->default('internal');
            $table->string('status', 255)->nullable()->default(null);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('referal_codes'); } 
};
