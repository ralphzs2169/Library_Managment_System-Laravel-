<?php

namespace App\Policies;

use App\Enums\BorrowTransactionStatus;

class ReturnPolicy
{
    public static function canBeReturned($borrowTransaction, $returner)
    {
        if ($borrowTransaction->status === BorrowTransactionStatus::BORROWED || $borrowTransaction->status === BorrowTransactionStatus::OVERDUE) {
            return ['result' => 'success', 'returner_fullname' => $returner->full_name];
        }
        return ['result' => 'business_rule_violation', 'message' => 'The book cannot be returned.'];
    }
}