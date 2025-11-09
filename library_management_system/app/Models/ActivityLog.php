<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ActivityLog extends Model
{   
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_ACTIVATED = 'activated';
    const ACTION_DEACTIVATED = 'deactivated';
    const ACTION_BORROWED = 'borrowed';
    const ACTION_RESERVED = 'reserved';
    const ACTION_PAID = 'paid';
    const ACTION_AUTO_ENDED = 'auto_ended';

    const ACTIONS = [
        self::ACTION_CREATED,
        self::ACTION_UPDATED,
        self::ACTION_DELETED,
        self::ACTION_ACTIVATED,
        self::ACTION_DEACTIVATED,
        self::ACTION_BORROWED,
        self::ACTION_RESERVED,
        self::ACTION_PAID,
        self::ACTION_AUTO_ENDED
    ];

    protected $fillable = [
        'action',
        'details',
        'entity_type',
        'entity_id',
        'user_id',
        'semester_id',
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
