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
        return $this->belongsToMany(Book::class);
    }

    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory;
}
