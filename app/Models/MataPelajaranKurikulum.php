<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataPelajaranKurikulum extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'mata_pelajaran_kurikulum';

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: mata_pelajaran_kurikulum -> kurikulum -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('kurikulum.jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }
    protected $fillable = [
        'id_kurikulum',
        'id_mata_pelajaran_master',
        'semester'
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    public function mataPelajaranMaster()
    {
        return $this->belongsTo(MataPelajaranMaster::class, 'id_mata_pelajaran_master');
    }
}
