<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SarprasKategori extends Model
{
    protected $fillable = ['nama_kategori', 'deskripsi'];

    public function barangs()
    {
        return $this->hasMany(SarprasBarang::class, 'sarpras_kategori_id');
    }
}
