<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DosenBuku extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_dosen',
        'id_staff',
        'judul_buku',
        'tahun_buku',
        'isbn',
        'link_isbn',
        'penerbit',
    ];

    public function dosen()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen');
    }
}
