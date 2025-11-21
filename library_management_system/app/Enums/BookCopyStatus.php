<?php 

namespace App\Enums;

class BookCopyStatus {
    const AVAILABLE = 'available';
    const BORROWED = 'borrowed';
    const LOST = 'lost';
    const DAMAGED = 'damaged';
    const WITHDRAWN = 'withdrawn';
    const PENDING_ISSUE_REVIEW = 'pending_issue_review';
}

