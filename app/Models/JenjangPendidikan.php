<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenjangPendidikan extends Model
{
    use HasFactory;
    protected $table = 'jenjang_pendidikan';
    protected $fillable = ['nama', 'deskripsi', 'type'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_jenjang_pendidikan');
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'id_jenjang_pendidikan');
    }

    public function pendaftar()
    {
        return $this->hasMany(SiswaDataPendaftar::class, 'id_jenjang_pendidikan');
    }
}
