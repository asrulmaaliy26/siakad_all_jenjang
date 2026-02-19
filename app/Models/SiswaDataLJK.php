<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaDataLJK extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'siswa_data_ljk';

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: siswa_data_ljk -> mata_pelajaran_kelas -> kelas -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('mataPelajaranKelas.kelas.jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }

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
        'nilai' => 'float'
    ];

    /* ================= RELATIONS ================= */
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
