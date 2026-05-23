<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PklPendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_pkl_periode',
        'id_pkl_lembaga',
        'id_siswa_data',
        'status',
        'tgl_daftar',
    ];

    public function periode()
    {
        return $this->belongsTo(PklPeriode::class, 'id_pkl_periode');
    }

    public function lembaga()
    {
        return $this->belongsTo(PklLembaga::class, 'id_pkl_lembaga');
    }

    public function siswaData()
    {
        return $this->belongsTo(SiswaData::class, 'id_siswa_data');
    }
}
