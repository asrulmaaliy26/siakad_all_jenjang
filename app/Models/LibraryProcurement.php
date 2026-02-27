<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryProcurement extends Model
{
    use HasFactory;

    protected $table = 'library_procurements';

    protected $fillable = [
        'reference_no',
        'vendor',
        'total_amount',
        'procurement_date',
        'staff_id',
        'notes',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items()
    {
        return $this->hasMany(LibraryProcurementDetail::class, 'library_procurement_id');
    }
}
