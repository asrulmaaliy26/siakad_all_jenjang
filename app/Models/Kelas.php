<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RefOption\ProgramKelas;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = [
        'ro_program_kelas',
        'semester',
        'id_tahun_akademik',
        'id_jurusan',
        'status_aktif'
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    public function programKelas()
    {
        return $this->belongsTo(ProgramKelas::class, 'ro_program_kelas');
    }

    public function mataPelajaranKelas()
    {
        return $this->hasMany(
            MataPelajaranKelas::class,
            'id_kelas',
            'id'
        );
    }


    public function siswaDataLjk()
    {
        return $this->hasManyThrough(
            SiswaDataLJK::class,
            MataPelajaranKelas::class,
            'id_kelas',
            'id_mata_pelajaran_kelas',
            'id',
            'id'
        );
    }
}
