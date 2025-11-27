<?php

namespace App\Policies;

use App\Enums\BookCopyStatus;
use App\Models\User;
use App\Models\Book;
use App\Enums\ReservationStatus;

class BookPolicy
{

    public static function canBeReserved(Book $book, $user)
    {   
        // 1. Check if user already has an active reservation for this book
        $existingReservation = $user->reservations()
            ->where('book_id', $book->id)
            ->whereIn('status', [
                ReservationStatus::PENDING,
                ReservationStatus::READY_FOR_PICKUP
            ])
            ->first();

        if ($existingReservation) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'This member already has an active reservation for this book.'
            ];
        }
        
        // 2. Check available copies 
        $availableCopiesCount = $book->copies
            ->where('status', BookCopyStatus::AVAILABLE)
            ->count();

        // If there are more than one available copy, reservation should NOT be allowed
        if ($availableCopiesCount > 1) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'This book has multiple available copies and cannot be reserved. Please borrow a copy directly.'
            ];
        }

        // If exactly one available copy, reservation should NOT be allowed unless there is a ready for pickup reservation
        if ($availableCopiesCount === 1) {
            $hasReadyPickupReservation = $book->reservations()
                ->where('status', ReservationStatus::READY_FOR_PICKUP)
                ->exists();

            if (!$hasReadyPickupReservation) {
                return [
                    'result' => 'business_rule_violation',
                    'message' => 'This book has an available copy and cannot be reserved. Please borrow it directly.'
                ];
            }
        }

        // If availableCopiesCount is 0, reservation is allowed

        // 3. Check if a user already has borrowed a copy of this book
        $existingBorrow = $user->borrowTransactions()
            ->whereHas('bookCopy', function ($q) use ($book) {
                $q->where('book_id', $book->id);
            })
            ->where('status', BookCopyStatus::BORROWED)
            
            ->first();  
        if ($existingBorrow) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'This member has already borrowed a copy of this book.'
            ];
        }   
        
        // 4. Check maximum queue length for the book
        $maxQueueLength = (int) config('settings.reservation.queue_max_length');
        $currentQueueLength = $book->reservations()
            ->where('status', ReservationStatus::PENDING)
            ->count();

        if ($currentQueueLength >= $maxQueueLength) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'The reservation queue for this book has reached its maximum length.'
            ];
        }

        // All copies are borrowed → reservation allowed
        return ['result' => 'success'];
    }

}
