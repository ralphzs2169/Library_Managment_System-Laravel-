<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'penalty_id', 
        'paid_by_id', 
        'processed_by_id', 
        'amount', 
        'paid_at',
        'semester_id',
    ];

    public function penalty()
    {
        return $this->belongsTo(Penalty::class);
    }
}
