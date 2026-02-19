<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fakultas extends Model
{
    use HasFactory;
    protected $table = 'fakultas';
    protected $fillable = ['nama'];

    public function jurusan()
    {
        return $this->hasMany(Jurusan::class, 'id_fakultas');
    }
}
