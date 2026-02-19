<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAkademik extends Model
{
    use HasFactory;
    protected $table = 'tahun_akademik';
    protected $fillable = ['nama', 'periode', 'status'];

    public function pengaturanPendaftaran()
    {
        return $this->hasMany(PengaturanPendaftaran::class, 'id_tahun_akademik');
    }
}
