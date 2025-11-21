<?php

namespace App\Models;

use App\Enums\IssueReportStatus;
use App\Enums\IssueReportType;
use Illuminate\Database\Eloquent\Model;

class IssueReport extends Model
{
    protected $fillable = [
        'book_copy_id',
        'borrower_id',
        'reported_by',
        'approved_by',
        'report_type',
        'description',
        'status',
        'resolved_at',
    ];

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }
    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}