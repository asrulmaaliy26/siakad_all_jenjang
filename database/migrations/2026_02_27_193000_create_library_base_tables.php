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
        Schema::create('library_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        Schema::create('library_publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_categories');
        Schema::dropIfExists('library_publishers');
        Schema::dropIfExists('library_authors');
    }
};
