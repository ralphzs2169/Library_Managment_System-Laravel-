<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $settings = [
            // Borrowing rules
            ['key' => 'borrowing.max_books_per_student', 'value' => 3, 'group' => 'borrowing'],
            ['key' => 'borrowing.max_books_per_teacher', 'value' => 5, 'group' => 'borrowing'],
            ['key' => 'borrowing.student_duration', 'value' => 7, 'group' => 'borrowing'],
            ['key' => 'borrowing.teacher_duration', 'value' => 14, 'group' => 'borrowing'],


            // Renewal Rules
            ['key' => 'renewing.student_renewal_limit', 'value' => 2, 'group' => 'renewing'],
            ['key' => 'renewing.teacher_renewal_limit', 'value' => 3, 'group' => 'renewing'],
            ['key' => 'renewing.student_duration', 'value' => 7, 'group' => 'renewing'],
            ['key' => 'renewing.teacher_duration', 'value' => 10, 'group' => 'renewing'],
            ['key' => 'renewing.student_min_days_before_renewal', 'value' => 3, 'group' => 'renewing'],
            ['key' => 'renewing.teacher_min_days_before_renewal', 'value' => 1, 'group' => 'renewing'],

            // Penalty settings
            ['key' => 'penalty.rate_per_day', 'value' => 3.32, 'group' => 'penalty'],
            ['key' => 'penalty.max_amount', 'value' => 200, 'group' => 'penalty'],
            ['key' => 'penalty.lost_fee_multiplier', 'value' => 1.5, 'group' => 'penalty'],
            ['key' => 'penalty.damaged_fee_multiplier', 'value' => 0.75, 'group' => 'penalty'],

            // Notifications
            ['key' => 'notifications.reminder_days_before_due', 'value' => 3, 'group' => 'notifications'],
            ['key' => 'notifications.enable_borrower_notifications', 'value' => 1, 'group' => 'notifications'],
            ['key' => 'notifications.show_overdue_notifications', 'value' => 1, 'group' => 'notifications'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Default system settings seeded successfully.');
    }
}
