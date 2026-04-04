<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SarprasSuratKategori extends Model
{
    protected $table = 'sarpras_surat_kategoris';
    
    protected $fillable = [
        'nama',
        'kode',
        'format_nomor',
    ];

    public function suratKeluars()
    {
        return $this->hasMany(SarprasSuratKeluar::class);
    }
}
