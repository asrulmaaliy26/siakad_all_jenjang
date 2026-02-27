<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DosenPenelitian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_dosen',
        'id_staff',
        'judul_penelitian',
        'th_penelitian',
        'dana_penelitian',
        'tingkat_penelitian',
        'lokasi_file',
    ];

    public function dosen()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen');
    }
}
