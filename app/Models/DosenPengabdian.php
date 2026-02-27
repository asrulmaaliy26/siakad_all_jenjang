<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DosenPengabdian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_dosen',
        'id_staff',
        'judul_pengabdian',
        'tahun_pengabdian',
        'dana_pengabdian',
        'tingkat_pengabdian',
        'lokasi_file',
    ];

    public function dosen()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen');
    }
}
