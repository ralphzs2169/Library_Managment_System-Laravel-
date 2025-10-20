<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'cover_image',
        'title',
        'isbn',
        'description',
        'publisher',
        'publication_year',
        'copies_available',
        'language',
        'price',
        'genre_id',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_books', 'book_id', 'author_id');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;
}
