<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenewalTransaction extends Model
{
    protected $fillable = [
        'staff_id',
        'borrow_transaction_id',
        'renewed_at',
        'previous_due_at',
        'new_due_at',
    ];

    public function borrowTransactions(){
        return $this->belongsTo(BorrowTransaction::class);
    }
}
