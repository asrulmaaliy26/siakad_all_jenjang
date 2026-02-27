<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
    use HasFactory;

    protected $table = 'library_books';

    protected $fillable = [
        'title',
        'isbn',
        'library_author_id',
        'library_publisher_id',
        'library_category_id',
        'year',
        'stock',
        'total_borrows',
        'location',
        'cover_image',
    ];

    public function author()
    {
        return $this->belongsTo(LibraryAuthor::class, 'library_author_id');
    }

    public function publisher()
    {
        return $this->belongsTo(LibraryPublisher::class, 'library_publisher_id');
    }

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'library_category_id');
    }

    public function loans()
    {
        return $this->belongsToMany(LibraryLoan::class, 'library_loan_details');
    }
}
