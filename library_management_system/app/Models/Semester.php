<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date'
    ];

    public function borrowTransactions()
    {
        return $this->hasMany(BorrowTransaction::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function clearances()
    {
        return $this->hasMany(Clearance::class);
    }

    public function getActiveSemesterAttribute()
    {
        return Semester::where('status', 'active')->first();
    }
}
