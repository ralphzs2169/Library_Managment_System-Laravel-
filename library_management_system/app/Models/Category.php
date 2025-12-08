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

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'entity');
    }

    public function getGenresCountAttribute()
    {
        return $this->genres()->count();
    }

    public function getBooksCountAttribute()
    {
        return $this->genres()->withCount('books')->get()->sum('books_count');
    }

    use HasFactory;
}
