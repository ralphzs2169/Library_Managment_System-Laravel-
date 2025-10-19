<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'middle_initial',
    ];

    function books()
    {
        return $this->belongsToMany(Book::class, 'book_authors', 'author_id', 'book_id');
    }

    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory;
}
