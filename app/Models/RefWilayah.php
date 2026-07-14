<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefWilayah extends Model
{
    use HasFactory;

    protected $table = 'ref_wilayahs';
    
    protected $primaryKey = 'id_wil';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_wil',
        'kecamatan',
        'kabupaten',
        'provinsi',
    ];
}
