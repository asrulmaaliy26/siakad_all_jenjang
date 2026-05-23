<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PklLembaga extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'profil',
        'file_kerjasama',
        'kontak',
        'website',
    ];

    public function pklPeriodes()
    {
        return $this->belongsToMany(PklPeriode::class, 'pkl_periode_lembagas', 'id_pkl_lembaga', 'id_pkl_periode')
            ->withPivot('id', 'kuota')
            ->withTimestamps();
    }

    public function pendaftarans()
    {
        return $this->hasMany(PklPendaftaran::class, 'id_pkl_lembaga');
    }
}
