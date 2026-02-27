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
        Schema::table('pekan_ujian', function (Blueprint $table) {
            if (Schema::hasColumn('pekan_ujian', 'id_jenjang_pendidikan')) {
                // Check if FK exists before dropping. In MySQL we often just try/catch or use schema check.
                // Assuming FK name from previous error: FK_pekanujian_jenjangpendidikan
                try {
                    $table->dropForeign('FK_pekanujian_jenjangpendidikan');
                } catch (\Exception $e) {
                }
                $table->dropColumn('id_jenjang_pendidikan');
            }
        });

        Schema::table('jurusan', function (Blueprint $table) {
            if (Schema::hasColumn('jurusan', 'id_jenjang_pendidikan')) {
                try {
                    $table->dropForeign('fk_jurusan_jenjang_pendidikan');
                } catch (\Exception $e) {
                }
                $table->dropColumn('id_jenjang_pendidikan');
            }
        });

        Schema::dropIfExists('jenjang_pendidikan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('jenjang_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('deskripsi')->nullable();
            $table->string('type')->default('kampus');
            $table->timestamps();
        });

        Schema::table('jurusan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_jenjang_pendidikan')->nullable();
            $table->foreign('id_jenjang_pendidikan', 'fk_jurusan_jenjang_pendidikan')->references('id')->on('jenjang_pendidikan')->onDelete('cascade');
        });
    }
};
