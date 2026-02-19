<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferenceOption extends Model
{
    use HasFactory;
    protected $table = 'reference_option';
    protected $fillable = [
        'nama_grup',
        'kode',
        'nilai',
        'status',
        'deskripsi',
    ];
}
