<?php 

namespace App\Enums;

class ActivityLogActions {
    const CREATED = 'created';
    const UPDATED = 'updated';
    const DELETED = 'deleted';
    const ACTIVATED = 'activated';
    const DEACTIVATED = 'deactivated';
    const BORROWED = 'borrowed';
    const CREATE_RESERVATION = 'create_reservation';
    const RESERVED = 'reserved';
    const PAID = 'paid';
    const AUTO_ENDED = 'auto_ended';
    const RENEWED = 'renewed';
    const READY_FOR_PICKUP = 'reservation_ready_for_pickup';

}
