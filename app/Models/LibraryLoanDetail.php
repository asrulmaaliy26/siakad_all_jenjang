<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryLoanDetail extends Model
{
    use HasFactory;

    protected $table = 'library_loan_details';

    protected $fillable = [
        'library_loan_id',
        'library_book_id',
    ];

    public function loan()
    {
        return $this->belongsTo(LibraryLoan::class, 'library_loan_id');
    }

    public function book()
    {
        return $this->belongsTo(LibraryBook::class, 'library_book_id');
    }
}
