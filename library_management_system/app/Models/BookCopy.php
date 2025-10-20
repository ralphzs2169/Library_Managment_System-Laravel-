<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    protected $fillable = [
        'book_id',
        'status',
        'copies_available',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
