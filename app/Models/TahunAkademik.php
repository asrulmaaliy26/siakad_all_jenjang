<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAkademik extends Model
{
    use HasFactory;
    protected $table = 'tahun_akademik';
    protected $fillable = ['nama', 'periode', 'status'];

    public function pengaturanPendaftaran()
    {
        return $this->hasMany(PengaturanPendaftaran::class, 'id_tahun_akademik');
    }

    public function pengajuanJudul()
    {
        return $this->hasMany(TaPengajuanJudul::class, 'id_tahun_akademik');
    }

    public function seminarProposal()
    {
        return $this->hasMany(TaSeminarProposal::class, 'id_tahun_akademik');
    }

    public function skripsi()
    {
        return $this->hasMany(TaSkripsi::class, 'id_tahun_akademik');
    }
}
