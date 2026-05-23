<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PklPeriode extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_tahun_akademik',
        'nama',
        'tgl_mulai',
        'tgl_selesai',
        'is_active',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    public function lembagas()
    {
        return $this->belongsToMany(PklLembaga::class, 'pkl_periode_lembagas', 'id_pkl_periode', 'id_pkl_lembaga')
            ->withPivot('id', 'kuota')
            ->withTimestamps();
    }

    public function pendaftarans()
    {
        return $this->hasMany(PklPendaftaran::class, 'id_pkl_periode');
    }
}
