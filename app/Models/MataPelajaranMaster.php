<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataPelajaranMaster extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'mata_pelajaran_master';

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: mata_pelajaran_master -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }
    protected $fillable = ['nama', 'kode_feeder', 'id_jurusan', 'bobot', 'ro_jenis'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }
}
