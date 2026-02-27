<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaDataLJK extends Model
{
    use HasFactory;

    protected $table = 'siswa_data_ljk';


    // protected $primaryKey = 'id_data_ljk';

    protected $fillable = [
        'id_akademik_krs',
        'id_mata_pelajaran_kelas',
        'nilai',
        'ljk_simulasi',
        'ljk_uas',
        'artikel_uas',
        'tgl_upload_ljk_uas',
        'tgl_upload_artikel_uas',
        'ljk_uts',
        'artikel_uts',
        'tgl_upload_ljk_uts',
        'tgl_upload_artikel_uas',
        'tugas',
        'tgl_upload_tugas',
        'ljk_tugas_1',
        'ctt_tugas_1',
        'ljk_tugas_2',
        'ctt_tugas_2',
        'ljk_tugas_3',
        'ctt_tugas_3',
        'Nilai_UTS',
        'Nilai_TGS_1',
        'Nilai_TGS_2',
        'Nilai_TGS_3',
        'ljk_tugas_4',
        'ctt_tugas_4',
        'Nilai_TGS_4',
        'ljk_tugas_5',
        'ctt_tugas_5',
        'Nilai_TGS_5',
        'ljk_tugas_6',
        'ctt_tugas_6',
        'Nilai_TGS_6',
        'ljk_tugas_7',
        'ctt_tugas_7',
        'Nilai_TGS_7',
        'ljk_tugas_8',
        'ctt_tugas_8',
        'Nilai_TGS_8',
        'ljk_tugas_9',
        'ctt_tugas_9',
        'Nilai_TGS_9',
        'ljk_tugas_10',
        'ctt_tugas_10',
        'Nilai_TGS_10',
        'ljk_tugas_11',
        'ctt_tugas_11',
        'Nilai_TGS_11',
        'ljk_tugas_12',
        'ctt_tugas_12',
        'Nilai_TGS_12',
        'Nilai_UAS',
        'Nilai_Performance',
        'Nilai_Akhir',
        'Nilai_Huruf',
        'Status_Nilai',
        'Rekom_Nilai',
        'ket',
        'transfer',
        'cekal_kuliah',
        'ctt_uts',
        'ctt_uas',
    ];

    protected $casts = [
        'nilai'       => 'float',
        'ljk_uts'     => 'array',
        'artikel_uts' => 'array',
        'ljk_uas'     => 'array',
        'artikel_uas' => 'array',
        'ljk_tugas_1' => 'array',
        'ljk_tugas_2' => 'array',
        'ljk_tugas_3' => 'array',
        'ljk_tugas_4' => 'array',
        'ljk_tugas_5' => 'array',
        'ljk_tugas_6' => 'array',
        'ljk_tugas_7' => 'array',
        'ljk_tugas_8' => 'array',
        'ljk_tugas_9' => 'array',
        'ljk_tugas_10' => 'array',
        'ljk_tugas_11' => 'array',
        'ljk_tugas_12' => 'array',
    ];

    /* ================= RELATIONS ================= */
    public function getBobotAttribute()
    {
        $nilaiAngka = $this->Nilai_Akhir ?? 0;

        if ($nilaiAngka >= 85) return 4.0;
        if ($nilaiAngka >= 80) return 3.7;
        if ($nilaiAngka >= 75) return 3.3;
        if ($nilaiAngka >= 70) return 3.0;
        if ($nilaiAngka >= 65) return 2.7;
        if ($nilaiAngka >= 60) return 2.3;
        if ($nilaiAngka >= 55) return 2.0;
        if ($nilaiAngka >= 50) return 1.0;

        return 0.0;
    }

    public function akademikKrs()
    {
        return $this->belongsTo(AkademikKrs::class, 'id_akademik_krs');
    }

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(
            MataPelajaranKelas::class,
            'id_mata_pelajaran_kelas'
        );
    }
}
