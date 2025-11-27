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

    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;
}
