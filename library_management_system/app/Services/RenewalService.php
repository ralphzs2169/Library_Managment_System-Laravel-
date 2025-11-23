<?php

namespace App\Services;

use App\Models\BorrowTransaction;
use App\Models\RenewalTransaction; 
use App\Models\User;
use App\Models\BookCopy;
use App\Models\ActivityLog;
use App\Enums\ActivityLogActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RenewalService
{
     public function renewBook(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $renewer = User::findOrFail($request->input('renewer_id'));

            $transaction = BorrowTransaction::where('user_id', $renewer->id)
                ->where('id', $request->input('transaction_id'))
                ->whereNull('returned_at')
                ->firstOrFail();

            $bookCopy = BookCopy::with('book')->findOrFail($transaction->book_copy_id);
            $book = $bookCopy->book;

            // Calculate new due date
            $previousDueDate = $transaction->due_at;
            $newDueDate = $request->input('new-due-date');

            // Update transaction
            $transaction->update([
                'due_at' => $newDueDate,
                'times_renewed' => $transaction->times_renewed + 1,
            ]);

            RenewalTransaction::create([
                'borrow_transaction_id' => $transaction->id,
                'staff_id' => $request->user()->id,
                'renewed_at' => now(),
                'previous_due_at' => $previousDueDate,
                'new_due_at' => $newDueDate,
            ]);

            // Log activity
            ActivityLog::create([
                'action' => ActivityLogActions::RENEWED,
                'details' => $renewer->full_name . ' renewed "' . $book->title . '" (Copy #' . $bookCopy->copy_number . ') to new due date ' . Carbon::parse($newDueDate)->toDateString(),
                'entity_type' => 'Borrow Transaction',
                'entity_id' => $transaction->id,
                'user_id' => $request->user()->id,
            ]);

            return $transaction;
        });
    }
}   