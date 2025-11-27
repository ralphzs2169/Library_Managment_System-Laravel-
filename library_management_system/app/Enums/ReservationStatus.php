<?php 

namespace App\Enums;

class ReservationStatus
{
    const PENDING = 'pending';
    const READY_FOR_PICKUP = 'ready_for_pickup';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';
    const EXPIRED = 'expired';
}