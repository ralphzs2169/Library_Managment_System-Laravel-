<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;

class reservation extends Model
{
    protected $fillable = [
        'borrower_id',
        'book_id',
        'status',
        'pickup_deadline',
        'created_by_id',
        'created_by',
    ];

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getQueuePositionAttribute()
    {
        return Reservation::where('book_id', $this->book_id)
            ->where('status', ReservationStatus::PENDING)
            ->orderBy('created_at')
            ->pluck('id')
            ->search($this->id) + 1;
    }
}
