<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenDokumen extends Model
{
    use HasFactory;

    protected $table = 'dosen_dokumen';

    protected $fillable = [
        'id_dosen',
        'judul_dokumen',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'tipe_dokumen',
        'deskripsi',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get the dosen that owns the dokumen.
     */
    public function dosenData()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen');
    }

    /**
     * The jurnal pengajaran that belong to the dokumen.
     */
    public function jurnalPengajaran()
    {
        return $this->belongsToMany(JurnalPengajaran::class, 'jurnal_dokumen', 'id_dokumen', 'id_jurnal')
            ->withTimestamps();
    }
}
