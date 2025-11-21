<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\BorrowTransactionStatus;

class BorrowTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'book_copy_id',
        'semester_id',
        'borrowed_at',
        'returned_at',
        'due_at',
        'status',
    ];

    protected $casts = [
        'current_penalty' => 'float',
        'days_overdue' => 'integer',
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function markAsBorrowed()
    {
        $this->status = BorrowTransactionStatus::BORROWED;
        $this->save();
    }

    public function markAsReturned()
    {
        $this->status = BorrowTransactionStatus::RETURNED;
        $this->returned_at = now();
        $this->save();
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookCopy()
    {
         return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }
}
