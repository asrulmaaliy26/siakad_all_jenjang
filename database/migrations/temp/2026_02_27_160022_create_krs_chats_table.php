<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::disableForeignKeyConstraints();
        Schema::create('krs_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down(): void { Schema::dropIfExists('krs_chats'); } 
};
