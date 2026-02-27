<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiSiswa extends Model
{
    use HasFactory;

    protected $table = 'absensi_siswa';

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
