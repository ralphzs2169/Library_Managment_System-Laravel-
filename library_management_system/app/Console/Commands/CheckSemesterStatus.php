<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Semester;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CheckSemesterStatus extends Command
{
    protected $signature = 'semesters:check-status';
    protected $description = 'Automatically end the active semester if its end date has passed, and log the action.';

    public function handle()
    {
        $today = Carbon::today();

        $activeSemester = Semester::where('status', 'active')->first();

        if (!$activeSemester) {
            $this->info("No active semester found.");
            return Command::SUCCESS;
        }

        // Check if the semester's end date has passed (use <= to include today)
        $semesterEndDate = Carbon::parse($activeSemester->end_date);
        
        if ($semesterEndDate->lte($today)) {
            try {
                DB::transaction(function () use ($activeSemester, $today) {     
                    $activeSemester->status = 'ended';
                    $activeSemester->save();

                    // Log this automatic action in activity_logs
                    ActivityLog::create([
                        'entity_type' => 'Semester',
                        'entity_id' => $activeSemester->id,
                        'action' => 'auto_ended',
                        'details' => "'{$activeSemester->name}' automatically marked as ended on {$today->toDateString()}.",
                        'user_id' => null, // system action
                    ]);
                });

                $this->info("Semester '{$activeSemester->name}' automatically marked as ended and logged.");
            } catch (\Exception $e) {
                $this->error("Failed to end semester: " . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->info("Active semester '{$activeSemester->name}' is still ongoing (ends on {$semesterEndDate->toDateString()}).");
        }

        return Command::SUCCESS;
    }
}
