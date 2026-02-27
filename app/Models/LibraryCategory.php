<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryCategory extends Model
{
    use HasFactory;

    protected $table = 'library_categories';

    protected $fillable = ['name'];

    public function books()
    {
        return $this->hasMany(LibraryBook::class, 'library_category_id');
    }
}
