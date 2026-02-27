<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryLoan extends Model
{
    use HasFactory;

    protected $table = 'library_loans';

    protected $fillable = [
        'riwayat_pendidikan_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
        'fine_amount',
        'staff_id',
    ];

    public function riwayatPendidikan()
    {
        return $this->belongsTo(RiwayatPendidikan::class, 'riwayat_pendidikan_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function books()
    {
        return $this->belongsToMany(LibraryBook::class, 'library_loan_details', 'library_loan_id', 'library_book_id');
    }

    public function details()
    {
        return $this->hasMany(LibraryLoanDetail::class, 'library_loan_id');
    }
}
