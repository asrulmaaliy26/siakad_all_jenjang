<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa_data_ljk', function (Blueprint $table) {
            $table->text('ctt_pelanggaran_uts')->nullable()->after('ctt_uts')->comment('Log pelanggaran selama ujian UTS');
            $table->text('ctt_pelanggaran_uas')->nullable()->after('ctt_uas')->comment('Log pelanggaran selama ujian UAS');
            $table->unsignedTinyInteger('jml_pelanggaran_uts')->default(0)->after('ctt_pelanggaran_uts');
            $table->unsignedTinyInteger('jml_pelanggaran_uas')->default(0)->after('ctt_pelanggaran_uas');
            $table->enum('cekal_ujian_uts', ['Y', 'N'])->default('N')->after('jml_pelanggaran_uts')->comment('Y = diblokir dari ujian UTS karena >=5 pelanggaran');
            $table->enum('cekal_ujian_uas', ['Y', 'N'])->default('N')->after('jml_pelanggaran_uas')->comment('Y = diblokir dari ujian UAS karena >=5 pelanggaran');
        });
    }

    public function down(): void
    {
        Schema::table('siswa_data_ljk', function (Blueprint $table) {
            $table->dropColumn([
                'ctt_pelanggaran_uts',
                'ctt_pelanggaran_uas',
                'jml_pelanggaran_uts',
                'jml_pelanggaran_uas',
                'cekal_ujian_uts',
                'cekal_ujian_uas',
            ]);
        });
    }
};
