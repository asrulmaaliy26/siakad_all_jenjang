<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataPelajaranKurikulum extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran_kurikulum';

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
