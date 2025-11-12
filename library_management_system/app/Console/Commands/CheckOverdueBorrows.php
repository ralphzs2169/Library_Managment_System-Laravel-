<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowTransaction;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CheckOverdueBorrows extends Command
{
    protected $signature = 'borrows:check-overdue';
    protected $description = 'Automatically mark borrow transactions as overdue if their due date has passed.';

    public function handle()
    {
        $today = Carbon::today();

        // Get all borrow transactions that are still 'borrowed' and past due
        $overdueBorrows = BorrowTransaction::where('status', 'borrowed')
            ->whereDate('due_at', '<', $today)
            ->get();

        if ($overdueBorrows->isEmpty()) {
            $this->info("No overdue borrow transactions found.");
            return Command::SUCCESS;
        }

        try {
            DB::transaction(function () use ($overdueBorrows, $today) {
                foreach ($overdueBorrows as $borrow) {
                    $borrow->status = 'overdue';
                    $borrow->save();

                    $borrower = User::find($borrow->user_id);
                    if ($borrower) {
                        $borrower->library_status = 'suspended';
                        $borrower->save();
                    }

                    // Log the action
                    ActivityLog::create([
                        'entity_type' => 'Borrow Transaction',
                        'entity_id' => $borrow->id,
                        'action' => 'marked overdue',
                        'details' => "Borrow transaction ID {$borrow->id} marked as overdue on {$today->toDateString()}.",
                        'user_id' => null, // system action
                    ]);
                }
            });

            $this->info(count($overdueBorrows) . " borrow transaction(s) marked as overdue and logged.");
        } catch (\Exception $e) {
            $this->error("Failed to update overdue borrows: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

