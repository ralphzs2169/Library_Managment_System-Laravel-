<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BookCopyStatus;

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
            ->where('status', 'pending');
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

    use HasFactory;
}
