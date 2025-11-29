<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('semesters:check-status')->everyMinute(); //testing
        $schedule->command('borrows:check-overdue')->everyMinute(); //testing
        $schedule->command('reservations:check-expired')->everyMinute(); //testing
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
