<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BookCopyStatus;
use App\Enums\IssueReportStatus;
use App\Models\Reservation;
use App\Enums\ReservationStatus;

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

    public function issueReport()
    {
        return $this->hasMany(IssueReport::class, 'book_copy_id');
    }

    public function pendingIssueReport()
    {
        return $this->hasOne(IssueReport::class)
            ->where('status', IssueReportStatus::PENDING);
    }

    public function borrowTransaction()
    {
        return $this->hasMany(BorrowTransaction::class, 'book_copy_id');
    }

    public function getPenaltyAmountAttribute()
    {
        $bookPrice = $this->book()->first()->price;
        
        return $bookPrice * config('settings.penalty.damaged_fee_multiplier'); // 50% of book price
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'book_copy_id');
    }

    public function assignedReservation()
    {
        return $this->hasOne(Reservation::class, 'book_copy_id')
            ->where('status', ReservationStatus::READY_FOR_PICKUP);
    }

    use HasFactory;
}
