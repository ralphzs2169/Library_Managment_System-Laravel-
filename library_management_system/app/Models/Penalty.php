<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PenaltyType;
use App\Enums\PenaltyStatus;

class Penalty extends Model
{
    protected $fillable = [
        'borrow_transaction_id',
        'amount',
        'type',
        'status',
        'issued_at',
    ];

    public function borrowTransaction()
    {
        return $this->belongsTo(BorrowTransaction::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
