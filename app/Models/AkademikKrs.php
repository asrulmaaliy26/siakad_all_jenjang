<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AkademikKrs extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'akademik_krs';

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: akademik_krs -> riwayat_pendidikan -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('riwayatPendidikan.jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }
    protected $fillable = [
        'id_riwayat_pendidikan',
        'id_kelas',
        'semester',
        'jumlah_sks',
        'tgl_krs',
        'kode_tahun',
        'status_bayar',
        'syarat_uts',
        'syarat_uas',
        'syarat_krs',
        'syarat_lain',
        'kwitansi_krs', // berkas / file uploud
        'berkas_lain', // berkas / file uploud
        'status_aktif',
    ];


    public function riwayatPendidikan()
    {
        return $this->belongsTo(RiwayatPendidikan::class, 'id_riwayat_pendidikan');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function siswaDataLjk()
    {
        return $this->hasMany(SiswaDataLJK::class, 'id_akademik_krs');
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'id_krs');
    }
}
