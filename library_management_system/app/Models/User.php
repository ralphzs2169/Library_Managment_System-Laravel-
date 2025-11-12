<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'middle_initial',
        'email',
        'username',
        'password',
        'contact_number',
        'role',
        'library_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullnameAttribute()
    {
        $middle_name = $this->middle_initial ? "{$this->middle_initial}. " : '';
        return "{$this->firstname} " . $middle_name . "{$this->lastname}";
    }

    public function getLibraryStatusLabelAttribute()
    {
        return $this->library_status === 'with_penalty' ? 'penalty' : $this->library_status;
    }

    public function getActiveBorrowingsCountAttribute()
    {
        return $this->borrowTransactions()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->whereNull('returned_at')
            ->count();
    }
    
    public function getTotalUnpaidFinesAttribute()
    {
        return $this->borrowTransactions()
            ->whereHas('penalties', function ($q) {
                $q->where('status', 'unpaid');
            })
            ->get()
            ->sum(function ($transaction) {
                return $transaction->penalties()
                    ->where('status', 'unpaid')
                    ->sum('amount');
            });
    }
    
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function students()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function teachers()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function borrowTransactions()
    {
        return $this->hasMany(BorrowTransaction::class, 'user_id');
    }

    
}
