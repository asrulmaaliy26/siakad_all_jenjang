<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RefOption\RuangKelas;

class MataPelajaranKelas extends Model
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
        'uts',
        'uas',
        'soal_uts',
        'soal_uas',
        'tgl_uts',
        'tgl_uas',
        'status_uts',
        'status_uas',
        'ruang_uts',
        'ruang_uas',
        'link_kelas',
        'passcode',
        'ctt_soal_uts',
        'ctt_soal_uas',
    ];

    protected $casts = [
        'soal_uts' => 'array',
        'soal_uas' => 'array',
    ];

    public function mataPelajaranKurikulum()
    {
        return $this->belongsTo(MataPelajaranKurikulum::class, 'id_mata_pelajaran_kurikulum');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function dosenData()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen_data');
    }

    public function ruangKelas()
    {
        return $this->belongsTo(ReferenceOption::class, 'ro_ruang_kelas');
    }

    public function pelaksanaanKelas()
    {
        return $this->belongsTo(ReferenceOption::class, 'ro_pelaksanaan_kelas');
    }

    public function pengawas()
    {
        return $this->belongsTo(DosenData::class, 'id_pengawas');
    }

    public function siswaDataLjk()
    {
        return $this->hasMany(SiswaDataLJK::class, 'id_mata_pelajaran_kelas');
    }

    public function pertemuanKelas()
    {
        return $this->hasMany(PertemuanKelas::class, 'id_mata_pelajaran_kelas');
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'id_mata_pelajaran_kelas');
    }

    public function jurnalPengajaran()
    {
        return $this->hasMany(JurnalPengajaran::class, 'id_mata_pelajaran_kelas');
    }

    // Accessors
    public function getNilaiRataRataAttribute()
    {
        return $this->siswaDataLjk()->avg('nilai') ?? 0;
    }

    public function getJumlahMahasiswaAttribute()
    {
        return $this->kelas ? $this->kelas->akademikKrs()->count() : 0;
    }

    public function getProgressAttribute()
    {
        $totalPertemuan = 16; // Asumsi standar 16 pertemuan
        $jumlahPertemuan = $this->pertemuanKelas()->count();
        return ($jumlahPertemuan / $totalPertemuan) * 100;
    }
    protected function statusUts(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn($value) => $value === 'Y',
            set: fn($value) => ($value === true || $value === 'Y' || $value === 1) ? 'Y' : 'N',
        );
    }

    protected function statusUas(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn($value) => $value === 'Y',
            set: fn($value) => ($value === true || $value === 'Y' || $value === 1) ? 'Y' : 'N',
        );
    }
}
