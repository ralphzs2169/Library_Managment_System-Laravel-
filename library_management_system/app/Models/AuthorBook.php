<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorBook extends Model
{
    // If your pivot table name differs, change this accordingly.
    protected $table = 'author_books';

    // Allow mass assignment for pivot columns.
    protected $fillable = [
        'book_id',
        'author_id',
    ];

    // Pivot tables typically don't have timestamps; set to true if yours do.
    public $timestamps = false;
}
