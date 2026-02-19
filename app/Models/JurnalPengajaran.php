<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalPengajaran extends Model
{
    use HasFactory;

    protected $table = 'dosen_jurnal_pengajaran';

    protected $fillable = [
        'judul',
        'id_mata_pelajaran_kelas',
        'description',
        'deadline',
        'status_akses',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    /**
     * Get the mata pelajaran kelas that owns the jurnal pengajaran.
     */
    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class, 'id_mata_pelajaran_kelas');
    }

    /**
     * The dokumen that belong to the jurnal pengajaran.
     */
    public function dokumen()
    {
        return $this->belongsToMany(DosenDokumen::class, 'jurnal_dokumen', 'id_jurnal', 'id_dokumen')
            ->withTimestamps();
    }
}
