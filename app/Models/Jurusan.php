<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurusan extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;
    protected $table = 'jurusan';
    protected $fillable = ['nama', 'id_fakultas', 'id_jenjang_pendidikan'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }
    /**
     * Get the jenjang pendidikan that owns the jurusan.
     */
    public function jenjangPendidikan()
    {
        return $this->belongsTo(JenjangPendidikan::class, 'id_jenjang_pendidikan');
    }
}
