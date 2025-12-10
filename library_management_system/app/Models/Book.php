<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ReservationStatus;

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

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }   
    
    public function readyToPickupCopies(Reservation $reservation)
    {
        return $this->with('books')
            ->where('reservation_id', $reservation->id)
            ->where('status', ReservationStatus::READY_FOR_PICKUP);
    }

    public function borrowTransactions()
    {
        return $this->hasManyThrough(BorrowTransaction::class, BookCopy::class);
    }
    
    public function getNewArrivalsAttribute()
    {
        return $this->whereYear('created_at', now()->year)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
    }

    public function getBorrowCountAttribute()
    {
        return $this->borrowTransactions()->count();
    }

    use HasFactory;
}
