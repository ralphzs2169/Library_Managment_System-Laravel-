<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\ReservationStatus;
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

     protected $appends = ['fullname', 'total_unpaid_fines'];

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
                $q->whereIn('status', ['unpaid', 'partially_paid']);
            })
            ->get()
            ->sum(function ($transaction) {
                return $transaction->penalties()
                    ->whereIn('status', ['unpaid', 'partially_paid'])
                    ->get() // get actual penalties
                    ->sum(function ($penalty) {
                        if ($penalty->status === 'partially_paid') {
                            return $penalty->amount - $penalty->payments()->sum('amount');
                        }
                        return $penalty->amount;
                    });
            });
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'borrower_id');
    }

    public function pendingReservations()
    {
        return $this->reservations()
            ->where('status', ReservationStatus::PENDING);
    }

    public function readyReservations()
    {
        return $this->reservations()
            ->where('status', ReservationStatus::READY_FOR_PICKUP);
    }

    public function activeReservations()
    {
        return $this->hasMany(Reservation::class, 'borrower_id')
            ->with(['book.author', 'book.genre.category', 'bookCopy'])
            ->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::READY_FOR_PICKUP]);
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

    public function penalties()
    {
        return $this->hasManyThrough(Penalty::class, BorrowTransaction::class, 'user_id', 'borrow_transaction_id');
    }

    public function issueReports()
    {
        return $this->hasMany(IssueReport::class, 'borrower_id');
    }

    public function reportsFiled()
    {
        return $this->hasMany(IssueReport::class, 'reported_by');
    }

    // Reports approved by the librarian/admin
    public function reportsApproved()
    {
        return $this->hasMany(IssueReport::class, 'approved_by');
    }

    public function clearances()
    {
        return $this->hasMany(Clearance::class, 'user_id');
    }
}
