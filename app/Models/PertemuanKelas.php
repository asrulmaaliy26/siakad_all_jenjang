<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PertemuanKelas extends Model
{
    use HasFactory;
    protected $table = 'pertemuan_kelas';
    protected $fillable = [
        'id_mata_pelajaran_kelas',
        'pertemuan_ke',
        'tanggal',
        'materi'
    ];

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(
            MataPelajaranKelas::class,
            'id_mata_pelajaran_kelas'
        );
    }
}
