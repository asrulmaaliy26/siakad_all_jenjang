<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryVisit extends Model
{
    use HasFactory;

    protected $table = 'library_visits';

    protected $fillable = [
        'riwayat_pendidikan_id',
        'visited_at',
        'purpose',
    ];

    public function riwayatPendidikan()
    {
        return $this->belongsTo(RiwayatPendidikan::class, 'riwayat_pendidikan_id');
    }
}
