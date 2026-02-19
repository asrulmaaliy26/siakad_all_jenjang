<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDokumen extends Model
{
    use HasFactory;

    protected $table = 'jurnal_dokumen';

    protected $fillable = [
        'id_jurnal',
        'id_dokumen',
    ];

    /**
     * Get the jurnal referencing this pivot.
     */
    public function jurnalPengajaran()
    {
        return $this->belongsTo(JurnalPengajaran::class, 'id_jurnal');
    }

    /**
     * Get the dokumen referencing this pivot.
     */
    public function dosenDokumen()
    {
        return $this->belongsTo(DosenDokumen::class, 'id_dokumen');
    }
}
