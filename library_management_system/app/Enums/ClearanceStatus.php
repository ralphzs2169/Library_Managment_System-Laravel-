<?php 

namespace App\Enums;

class ClearanceStatus
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const MISSED = 'missed';
    const INACTIVE_LAST_SEM = 'inactive_last_sem';
    const UNRESOLVED = 'unresolved';
}