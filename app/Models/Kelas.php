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
        'status_aktif',
        'kode_pddikti'
    ];

    protected static function booted()
    {
        static::saving(function ($kelas) {
            if (!empty($kelas->id_jurusan) && !empty($kelas->ro_program_kelas)) {
                $jurusan = \App\Models\Jurusan::find($kelas->id_jurusan);
                $programKelas = \App\Models\RefOption\ProgramKelas::find($kelas->ro_program_kelas);

                $jurNama = $jurusan->nama ?? '';
                $progNama = $programKelas->nilai ?? '';
                $smt = $kelas->semester ?? '';

                $prefix = '';
                if (str_contains(strtolower($jurNama), 'manajemen pendidikan')) {
                    $prefix = 'MPI';
                } elseif (str_contains(strtolower($jurNama), 'studi islam')) {
                    $prefix = 'SI';
                } elseif (str_contains(strtolower($jurNama), 'al-qur')) {
                    $prefix = 'IAT';
                } else {
                    $prefix = strtoupper(substr($jurNama, 0, 3));
                }

                $suffix = 'A';
                if (str_contains(strtolower($progNama), 'kelas a')) {
                    $suffix = 'A';
                } elseif (str_contains(strtolower($progNama), 'kelas b')) {
                    $suffix = 'B';
                } elseif (str_contains(strtolower($progNama), 'kelas c') || str_contains(strtolower($progNama), 'afiliasi')) {
                    $suffix = 'C';
                } elseif (str_contains(strtolower($progNama), 'reguler')) {
                    $suffix = 'A';
                } elseif (str_contains(strtolower($progNama), 'karyawan')) {
                    $suffix = 'B';
                }

                $kelas->kode_pddikti = strtoupper($prefix . $smt . $suffix);
            }
        });
    }

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
