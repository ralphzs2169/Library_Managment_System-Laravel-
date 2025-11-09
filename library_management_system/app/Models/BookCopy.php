<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    protected $fillable = [
        'book_id',
        'status',
        'copy_number',
        'is_archived'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    use HasFactory;
}
