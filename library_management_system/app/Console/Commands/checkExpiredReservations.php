<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Enums\ReservationStatus;
use App\Models\ActivityLog;
use App\Enums\ActivityLogActions;
use Illuminate\Support\Facades\DB;
use App\Models\BookCopy;
use App\Services\ReservationService;        

class checkExpiredReservations extends Command
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        parent::__construct();
    }

    protected $signature = 'reservations:check-expired';
    protected $description = 'Check for expired reservations and update their status';

    public function handle()
    {
        $now = Carbon::now();

        DB::transaction(function () use ($now) {
            $readyReservations = Reservation::where('status', ReservationStatus::READY_FOR_PICKUP)->get();

            $count = 0;
            foreach ($readyReservations as $reservation) {
                $deadline = $reservation->pickup_deadline;
                if ($deadline && $deadline < $now) {
                    $reservation->status = ReservationStatus::EXPIRED;
                    $reservation->save();

                    $user = $reservation->borrower;
                    $fullname = $user ? $user->fullname : 'Unknown';

                    ActivityLog::create([
                        'entity_type' => 'Reservation',
                        'entity_id' => $reservation->id,
                        'action' => ActivityLogActions::AUTO_EXPIRED,
                        'details' => "Reservation for {$fullname} marked as expired on {$now->toDateString()}.",
                        'user_id' => null, // system action
                    ]);

                    // Promote next pending reservation for the same book copy
                    if ($reservation->book_copy_id) {
                        $bookCopy = BookCopy::find($reservation->book_copy_id);
                        if ($bookCopy) {
                            $this->reservationService->promoteNextPendingReservation($bookCopy);
                        }
                    }
                    $count++;
                }
            }

            $this->info("Expired reservations processed: {$count}");
        });
    }
}
