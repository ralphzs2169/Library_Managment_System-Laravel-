<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
    /** @use HasFactory<\Database\Factories\CategoryGenreFactory> */

    public function genres()
    {
        return $this->hasMany(Genre::class);
    }

    use HasFactory;
}
