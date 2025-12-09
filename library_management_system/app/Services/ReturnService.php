<?php

namespace App\Services;

use App\Enums\BorrowTransactionStatus;
use App\Enums\BookCopyStatus;
use App\Enums\IssueReportStatus;
use App\Enums\IssueReportType;
use App\Enums\LibraryStatus;
use App\Enums\PenaltyType;
use App\Enums\PenaltyStatus;
use App\Enums\ReservationStatus;
use App\Models\ActivityLog;
use App\Models\BorrowTransaction;
use App\Models\BookCopy;
use App\Models\IssueReport;
use App\Models\Penalty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Semester;

class ReturnService 
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function performReturn(Request $request) 
    {
        return DB::transaction(function () use ($request) {
            $borrower = User::findOrFail($request->input('borrower_id'));
            $bookCopyId = $request->input('book_copy_id');
            $reportDamaged = $request->boolean('report_damaged');

            $transaction = BorrowTransaction::where('user_id', $borrower->id)
                ->where('book_copy_id', $bookCopyId)
                ->whereNull('returned_at')
                ->firstOrFail();

            $bookCopy = BookCopy::with('book')->findOrFail($bookCopyId);
            $book = $bookCopy->book;

            // Check if returned late
            $dueDate = \Carbon\Carbon::parse($transaction->due_at)->startOfDay();
            $returnedDate = now()->startOfDay();
            $isLate = $returnedDate->gt($dueDate);

            // Determine final transaction status and copy status
            $hasPenalty = false;

            if($isLate){
                $transactionStatus = BorrowTransactionStatus::RETURNED;
                $copyStatus = BookCopyStatus::AVAILABLE;

                $activeSemesterId = Semester::where('status', 'active')->value('id');

                $penalty = Penalty::create([
                    'borrow_transaction_id' => $transaction->id,
                    'amount' => min(round($dueDate->diffInDays($returnedDate) * (float)config('settings.penalty.rate_per_day', 0), 2), (float)config('settings.penalty.max_amount', 0)),
                    'type' => PenaltyType::LATE_RETURN,
                    'status' => PenaltyStatus::UNPAID,
                    'semester_id' => $activeSemesterId ?? null,
                    'issued_at' => now(),
                ]);

                if (!$penalty) {
                    throw new \Exception('Failed to create penalty record.');
                } else {
                    $hasPenalty = true;
                }

                if ($reportDamaged) {
                    // If also damaged, override statuses
                    $transactionStatus = BorrowTransactionStatus::RETURNED;
                    $copyStatus = BookCopyStatus::PENDING_ISSUE_REVIEW;

                    IssueReport::create([
                        'book_copy_id' => $bookCopy->id,
                        'borrower_id' => $borrower->id,
                        'reported_by' => $request->user()->id,
                        'report_type' => IssueReportType::DAMAGE,
                        'description' => $request->input('damage_description', 'Reported damaged upon return.'),
                        'status' => IssueReportStatus::PENDING,
                    ]);
                }
                
            } else if ($reportDamaged) {
                // If damaged, status is 'damaged' regardless of being late
                $transactionStatus = BorrowTransactionStatus::RETURNED;
                $copyStatus = BookCopyStatus::PENDING_ISSUE_REVIEW;

                    IssueReport::create([
                        'book_copy_id' => $bookCopy->id,
                        'borrower_id' => $borrower->id,
                        'reported_by' => $request->user()->id,
                        'report_type' => IssueReportType::DAMAGE,
                        'description' => $request->input('damage_description', 'Reported damaged upon return.'),
                        'status' => IssueReportStatus::PENDING,
                    ]);
            } else {
                // Not damaged - mark as 'returned'
                $transactionStatus = BorrowTransactionStatus::RETURNED;
                $copyStatus = BookCopyStatus::AVAILABLE;
            }

            // Update transaction
             $transaction->update([
                'returned_at' => now(),
                'status' => $transactionStatus,
            ]);

            // Update book copy status
            $bookCopy->update(['status' => $copyStatus]);

            if ($hasPenalty) {
                // Suspend borrower
                $borrower->update(['library_status' => LibraryStatus::SUSPENDED]);
            }

            // Create activity log
            $damagedText = $reportDamaged ? ' (reported as damaged)' : '';
            $lateText = $isLate && !$reportDamaged ? ' (returned late)' : '';
            ActivityLog::create([
                'action' => 'Returned a Book',
                'details' => $borrower->full_name . ' returned "' . $book->title . '" (Copy #' . $bookCopy->copy_number . ')' . $damagedText . $lateText,
                'entity_type' => 'Borrow Transaction',
                'entity_id' => $transaction->id,
                'user_id' => $request->user()->id,
            ]);

            $this->reservationService->promoteNextPendingReservation($bookCopy);

            return $transaction;
        });
    }


}