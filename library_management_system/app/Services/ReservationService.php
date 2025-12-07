<?php

namespace App\Services; 

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\ActivityLog;
use App\Enums\ActivityLogActions;
use App\Enums\ReservationStatus;
use App\Models\BookCopy;
use App\Policies\BookPolicy;
use App\Policies\ReservationPolicy;
use App\Enums\BookCopyStatus;
use App\Policies\BorrowPolicy;
use App\Models\Semester;

class ReservationService
{

    public function storeReservation(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $reserver = User::findOrFail($request->input('reserver_id'));

            $book = Book::findOrFail($request->input('book_id'));

            $activeSemester = Semester::where('status', 'active')->first();
            
            if (!$activeSemester) {
                throw new \Exception('No active semester found. Cannot proceed with borrowing.');
            }

            $reservation = Reservation::create([
                'borrower_id' => $reserver->id,
                'book_id' => $book->id,
                'status' => ReservationStatus::PENDING,
                'created_by_id' => $request->user()->id,
                'created_by' => $request->user()->role,
                'semester_id' => $activeSemester->id
            ]);

            // Log activity
            $createdBy = $request->user()->role === 'staff' ? 'staff' : 'borrower';

            $details = $reserver->full_name . ' reserved "' . $book->title . '"';

            if ($createdBy === 'staff') {
                $details .= ' (created by staff)';
            }

            ActivityLog::create([
                'action' => ActivityLogActions::CREATE_RESERVATION,
                'details' => $details,
                'entity_type' => 'Reservation',
                'entity_id' => $reservation->id,
                'user_id' => $request->user()->id,
                'created_by' => $createdBy,
            ]);


            return $reservation;
        });
    }

    public function updateReservationStatus(Reservation $reservation, $newStatus, $bookCopy = null)
    {
        return DB::transaction(function () use ($reservation, $newStatus, $bookCopy) {
            $oldStatus = $reservation->status;
            $reservation->update(['status' => $newStatus]);
            $reservation->save();

            if ($newStatus === ReservationStatus::READY_FOR_PICKUP) {
                $reservation->pickup_start_date = now();
                $reservation->book_copy_id = $bookCopy->id;
                $bookCopy->update(['status' => BookCopyStatus::ON_HOLD_FOR_PICKUP]);
                $bookCopy->save();
            } else if ($newStatus === ReservationStatus::CANCELLED) {
                $reservation->cancelled_at = now();
                
                if ($oldStatus === ReservationStatus::READY_FOR_PICKUP && $bookCopy) {
                    $bookCopy->update(['status' => BookCopyStatus::AVAILABLE]);
                    $bookCopy->save();
                }
            }

            $reservation->save();

            $actionType = match ($newStatus) {
                ReservationStatus::CANCELLED => ActivityLog::ACTION_CANCELLED,
                ReservationStatus::READY_FOR_PICKUP => ActivityLog::ACTION_READY_FOR_PICKUP,
                ReservationStatus::COMPLETED => ActivityLog::ACTION_COMPLETED,
                default => null,
            };

            ActivityLog::create([
                'action' => $actionType,
                'details' => 'Reservation for "' . $reservation->book->title . '" is ready for pickup.',
                'entity_type' => 'Reservation',
                'entity_id' => $reservation->id,
                'user_id' => null,
            ]);

            return $reservation;
        });
    }

    public function promoteNextPendingReservation(BookCopy $bookCopy)
    {
        $book = Book::findOrFail($bookCopy->book_id);

        $pendingReservation = $book->reservations()
            ->where('status', ReservationStatus::PENDING)
            ->orderBy('created_at', 'asc')
            ->first();

        $pendingIssueReport = $bookCopy->pendingIssueReport()->exists();
        
        if ($pendingReservation && !$pendingIssueReport) {
            $this->updateReservationStatus($pendingReservation, ReservationStatus::READY_FOR_PICKUP, $bookCopy);

            return true; 
        }

        return false; // no pending reservation
    }

     public function getAvailableCopiesForReservation(User $reserver, Book $book)
    {
        // 1. Check if reserver can reserve (do not block if they have a reservation, just inform)
        $canBorrow = BorrowPolicy::canBorrow(false, ['borrower_id' => $reserver->id]);

        if ($canBorrow['result'] !== 'success') {
            return [
                'result' => 'business_rule_violation',
                'message' => $canBorrow['message'],
                'available_copies' => []
            ];
        }

        // 2. Check if the book copy can be borrowed (do not block, just inform)
        $canBeBorrowed = BookCopyPolicy::canBeBorrowed($book, $reserver, true);

        // 3. Fetch available copies regardless of business rule violation
        $availableCopies = $book->copies()
            ->where('status', BookCopyStatus::AVAILABLE)
            ->get();

        // If there are available copies, always return them
        if ($availableCopies->count() > 0) {
            return [
                'result' => 'success',
                'available_copies' => $availableCopies,
                'message' => $canBeBorrowed['result'] !== 'success' ? $canBeBorrowed['message'] : null
            ];
        }

        // If no available copies, return business rule violation and empty array
        return [
            'result' => 'business_rule_violation',
            'message' => $canBeBorrowed['message'] ?? 'No available copies for this book.',
            'available_copies' => []
        ];
    }
}