<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KrsChat extends Model
{
    protected $fillable = ['id_dosen', 'user_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dosen()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen');
    }
}
