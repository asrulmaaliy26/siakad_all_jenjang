<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeWisuda extends Model
{
    protected $fillable = [
        'tahun',
        'periode_ke',
        'kuota',
        'pendaftar_count',
        'status',
        'tanggal_pelaksanaan'
    ];
}
