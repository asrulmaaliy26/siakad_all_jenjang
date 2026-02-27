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
        Schema::create('krs_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen')->index(); // Advisor ID (The Room)
            $table->unsignedBigInteger('user_id')->index();   // Sender User ID
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs_chats');
    }
};
