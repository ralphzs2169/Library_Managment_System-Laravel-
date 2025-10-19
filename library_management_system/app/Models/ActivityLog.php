<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ActivityLog extends Model
{
    protected $fillable = [
        'action',
        'details',
        'entity_type',
        'entity_id',
        'user_id',
    ];

    // Relationship to the user who performed the action (may be null for system actions)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function entity()
    {
        return $this->morphTo();
    }
    // public function librarian()
    // {
    //     return $this->belongsTo(Librarian::class);
    // }
    use HasFactory;
}
