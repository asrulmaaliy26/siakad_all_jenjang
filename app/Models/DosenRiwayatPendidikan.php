<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DosenRiwayatPendidikan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_dosen',
        'id_staff',
        'jenjang',
        'nama_pendidikan',
        'gelar_pendidikan',
        'th_lulus',
    ];

    public function dosen()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen');
    }
}
