<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $fillable = [
        'user_id',
        'objek',
        'bintang',
        'komentar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
