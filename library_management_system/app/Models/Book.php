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
        'author_id',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
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
