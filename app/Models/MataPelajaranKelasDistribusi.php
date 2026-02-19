<?php

namespace App\Models;

use App\Models\RefOption\PelaksanaanKelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RefOption\RuangKelas;

class MataPelajaranKelasDistribusi extends Model
{
    use HasFactory;
    protected $table = 'mata_pelajaran_kelas';
    protected $fillable = [
        'id_mata_pelajaran_kurikulum',
        'id_kelas',
        'id_dosen_data',
        'ro_ruang_kelas',
        'ro_pelaksanaan_kelas',
        'id_pengawas',
        'jumlah',
        'hari',
        'tanggal',
        'jam',
        'tgl_uts',
        'tgl_uas',
        'status_uts',
        'status_uas',
        'ruang_uts',
        'ruang_uas',
        'link_kelas',
        'passcode',
    ];

    public function mataPelajaranKurikulum()
    {
        return $this->belongsTo(
            MataPelajaranKurikulum::class,
            'id_mata_pelajaran_kurikulum'
        );
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function dosen()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen_data');
    }

    public function ruangKelas()
    {
        return $this->belongsTo(RuangKelas::class, 'ro_ruang_kelas');
    }
    public function pelaksanaanKelas()
    {
        return $this->belongsTo(PelaksanaanKelas::class, 'ro_pelaksanaan_kelas');
    }
    public function pertemuanKelas()
    {
        return $this->hasMany(
            PertemuanKelas::class,
            'id_mata_pelajaran_kelas',   // FK di tabel mata_pelajaran_kelas
            'id'          // PK di tabel kelas
        );
    }
}
