<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataPelajaranMaster extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran_master';

    protected $fillable = ['nama', 'kode_feeder', 'id_jurusan', 'bobot', 'jenis'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }
}
