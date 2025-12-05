<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\ReservationService;    
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Policies\ReservationPolicy;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Models\BookCopy;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function validateReservation(Request $request)
    {
        $user = User::find($request->input('reserver_id'));

        if (!$user) {
            return $this->jsonResponse('not_found', 'Reserver not found', 404);
        }

        $book = Book::find($request->input('book_id'));

        if (!$book) {
            return $this->jsonResponse('not_found', 'Book not found', 404);
        }

        $result = ReservationPolicy::canReserve($user, $book);
        
        switch ($result['result']) {
            case 'success': return $this->jsonResponse('valid', 'Validation passed', 200, ['reserver_fullname' => $result['reserver_fullname']]);
            case 'not_found': return $this->jsonResponse('not_found', $result['message'], 404);
            case 'invalid_input': return $this->jsonResponse('invalid_input', $result['message'], 422, ['errors' => $result['errors']]);
            case 'business_rule_violation': return $this->jsonResponse('business_rule_violation', $result['message'], 400);
            default: return $this->jsonResponse('error', 'Unknown validation error', 500);
        }
    }

    public function performReservation(Request $request)
    {
        try {
            $reservation = $this->reservationService->storeReservation($request);
            return $this->jsonResponse('success', 'Book reserved successfully', 200, ['reservation' => $reservation]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The book or transaction could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while reserving the book. Please try again later.', 500);
        }
    }
    
    public function cancelReservation(Request $request, $reservationId)
    {
        try {
            $reservation = Reservation::findOrFail($reservationId);
            $oldReservationStatus = $reservation->status;
            $check = ReservationPolicy::canBeCancelled($reservation);

            if ($check['result'] !== 'success') {
                return $this->jsonResponse('business_rule_violation', $check['message'], 400);
            }
            
            $bookCopy = ($reservation->status === ReservationStatus::READY_FOR_PICKUP) ? BookCopy::find($reservation->book_copy_id) : null;

            $this->reservationService->updateReservationStatus($reservation, ReservationStatus::CANCELLED, $bookCopy);
            
            if ($oldReservationStatus === ReservationStatus::READY_FOR_PICKUP && $reservation->book_copy_id) {
                if ($bookCopy) {
                    $this->reservationService->promoteNextPendingReservation($bookCopy);
                }
            }


            return $this->jsonResponse('success', 'Reservation cancelled successfully', 200, ['action_performer_role' => $request->user()->role]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The reservation could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'Something went wrong while cancelling the reservation. Please try again later.', 500);
        }
    }

    // New method to get available copies for completingreservation
    public function availableCopiesForReservation(Request $request, $borrowerId, $bookId)
    {
        try {
            $borrower = \App\Models\User::findOrFail($borrowerId);
            $book = Book::findOrFail($bookId);

            $availableCopies = $this->reservationService->getAvailableCopiesForReservation($borrower, $book);

            return $this->jsonResponse('success', 'Available copies retrieved successfully', 200, ['available_copies' => $availableCopies]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return $this->jsonResponse('error', 'The borrower or book could not be found.', 404);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->jsonResponse('error', $e . 'Something went wrong while retrieving available copies. Please try again later.', 500);
        }
    }
}
