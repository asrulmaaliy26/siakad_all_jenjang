<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WisudaMahasiswa extends Model
{
    protected $fillable = [
        'id_riwayat_pendidikan',
        'bebas_prodi',
        'bebas_fakultas',
        'bebas_perpustakaan',
        'bebas_keuangan',
        'nama_arab',
        'tempat_lahir_arab',
        'alamat_malang',
        'no_hp',
        'email',
        'pas_foto',
        'id_pembimbing_1',
        'id_pembimbing_2',
        'id_periode_wisuda',
        'status_pendaftaran',
        'tanggal_daftar'
    ];

    protected $casts = [
        'bebas_prodi' => 'boolean',
        'bebas_fakultas' => 'boolean',
        'bebas_perpustakaan' => 'boolean',
        'bebas_keuangan' => 'boolean',
    ];

    public function riwayatPendidikan()
    {
        return $this->belongsTo(RiwayatPendidikan::class, 'id_riwayat_pendidikan');
    }

    public function pembimbing1()
    {
        return $this->belongsTo(DosenData::class, 'id_pembimbing_1');
    }

    public function pembimbing2()
    {
        return $this->belongsTo(DosenData::class, 'id_pembimbing_2');
    }

    public function periodeWisuda()
    {
        return $this->belongsTo(PeriodeWisuda::class, 'id_periode_wisuda');
    }
}
