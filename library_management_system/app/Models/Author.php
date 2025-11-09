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

    public function getFullNameAttribute()
    {
        return "{$this->firstname} " . ($this->middle_initial ? "{$this->middle_initial}. " : '') . "{$this->lastname}";
    }

    public function getFormalNameAttribute()
    {
        return "{$this->lastname}, {$this->firstname}" . ($this->middle_initial ? " {$this->middle_initial}." : '');
    }

    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory;
}
