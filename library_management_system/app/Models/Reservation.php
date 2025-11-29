<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'borrower_id',
        'book_id',
        'status',
        'pickup_start_date',
        'created_by_id',
        'created_by',
    ];

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function getPickupDeadlineAttribute()
    {
        $role = $this->borrower->role ?? null;
        $windowDays = 0;

        if ($role === 'teacher') {
            $windowDays = (int) config('settings.reservation.teacher_pickup_window_days');
        } elseif ($role === 'student') {
            $windowDays = (int) config('settings.reservation.student_pickup_window_days');
        }

        $startDate = $this->pickup_start_date ?? $this->created_at;

        if ($windowDays && $startDate) {
            return Carbon::parse($startDate)->copy()->addDays($windowDays);
        }

        return null;
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
