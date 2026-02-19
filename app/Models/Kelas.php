<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RefOption\ProgramKelas;

class Kelas extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    public function scopeByJenjang($query, $jenjangId)
    {
        return $query->whereHas('jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }
    protected $table = 'kelas';
    protected $fillable = [
        'ro_program_kelas',
        'semester',
        // 'id_jenjang_pendidikan', // Removed as per request, derived from Jurusan
        'id_tahun_akademik',
        'id_jurusan',
        'status_aktif'
    ];

    // Relationship removed, access via $kelas->jurusan->jenjangPendidikan
    // public function jenjangPendidikan()
    // {
    //     return $this->belongsTo(JenjangPendidikan::class, 'id_jenjang_pendidikan');
    // }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    // Relasi ke ProgramKelas
    public function programKelas()
    {
        return $this->belongsTo(ProgramKelas::class, 'ro_program_kelas');
    }

    public function mataPelajaranKelas()
    {
        return $this->hasMany(
            MataPelajaranKelas::class,
            'id_kelas',   // FK di tabel mata_pelajaran_kelas
            'id'          // PK di tabel kelas
        );
    }
    public function akademikKrs()
    {
        return $this->hasMany(
            AkademikKrs::class,
            'id_kelas',   // FK di tabel mata_pelajaran_kelas
            'id'          // PK di tabel kelas
        );
    }
}
