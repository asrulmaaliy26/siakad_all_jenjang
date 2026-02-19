<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiSiswa extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'absensi_siswa';

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: absensi_siswa -> krs -> riwayat_pendidikan -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('krs.riwayatPendidikan.jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }
    protected $fillable = [
        'id_krs',
        'status',
        'id_mata_pelajaran_kelas',
        'waktu_absen'
    ];

    protected $dates = ['waktu_absen'];

    public function krs()
    {
        return $this->belongsTo(AkademikKrs::class, 'id_krs');
    }

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class, 'id_mata_pelajaran_kelas');
    }
}
