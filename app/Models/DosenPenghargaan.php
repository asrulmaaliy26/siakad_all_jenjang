<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DosenPenghargaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_dosen',
        'id_staff',
        'judul_penghargaan',
        'jenis_penghargaan',
        'tahun_penghargaan',
        'tingkat_penghargaan',
        'lokasi_file',
    ];

    public function dosen()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen');
    }
}
