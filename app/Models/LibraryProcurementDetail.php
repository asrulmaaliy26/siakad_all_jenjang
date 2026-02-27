<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryProcurementDetail extends Model
{
    use HasFactory;

    protected $table = 'library_procurement_details';

    protected $fillable = [
        'library_procurement_id',
        'library_book_id',
        'quantity',
        'unit_price',
    ];

    public function procurement()
    {
        return $this->belongsTo(LibraryProcurement::class, 'library_procurement_id');
    }

    public function book()
    {
        return $this->belongsTo(LibraryBook::class, 'library_book_id');
    }
}
