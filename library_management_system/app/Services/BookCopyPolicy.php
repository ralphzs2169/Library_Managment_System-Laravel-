<?php

namespace App\Services;

use App\Models\Book;;
use App\Enums\BookCopyStatus;
use App\Enums\ReservationStatus;

class BookCopyPolicy
{
    
    public static function canBeBorrowed(Book $book, $user, $isThroughReservation = false)
    {   
        // 1. Check if there are any available copies
        $availableCopies = $book->copies
        ->where('status', BookCopyStatus::AVAILABLE)
        ->count();

        if ($availableCopies < 1) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'No available copies for this book.'
            ];
        }

        // 2. Check if user has outstanding reservations for this book
        if (!$isThroughReservation) {
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
                    'message' => 'This member has an active reservation for this book and cannot borrow it directly.'
                ];
            }   
        }

        // 3. Check if user has a copy of this book already borrowed
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

        // 4. Check if the  book has a pending/ready to pickup reservation and no other available copies
        $hasActiveReservation = $book->reservations()
            ->whereIn('status', [
                ReservationStatus::PENDING,
                ReservationStatus::READY_FOR_PICKUP
            ])
            ->exists();
            
        $availableCopiesCount = $book->copies
            ->where('status', BookCopyStatus::AVAILABLE)
            ->count();
            
        if ($hasActiveReservation && $availableCopiesCount < 1) {
            return [
                'result' => 'business_rule_violation',
                'message' => 'This book has active reservations and cannot be borrowed directly.'
            ];
        }

        return ['result' => 'success'];
    }


}