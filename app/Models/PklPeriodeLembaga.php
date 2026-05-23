<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklPeriodeLembaga extends Model
{
    protected $table = 'pkl_periode_lembagas';

    protected $fillable = [
        'id_pkl_periode',
        'id_pkl_lembaga',
        'kuota',
    ];

    public function periode()
    {
        return $this->belongsTo(PklPeriode::class, 'id_pkl_periode');
    }

    public function lembaga()
    {
        return $this->belongsTo(PklLembaga::class, 'id_pkl_lembaga');
    }

    public function pendaftarans()
    {
        return $this->hasMany(PklPendaftaran::class, 'id_pkl_lembaga', 'id_pkl_lembaga')
            ->where('id_pkl_periode', $this->id_pkl_periode);
    }
}
