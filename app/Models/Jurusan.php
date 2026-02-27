<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurusan extends Model
{
    use HasFactory;
    protected $table = 'jurusan';
    protected $fillable = ['nama', 'id_fakultas'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'id_jurusan');
    }

    public function pendaftar()
    {
        return $this->hasMany(SiswaDataPendaftar::class, 'id_jurusan');
    }
}
