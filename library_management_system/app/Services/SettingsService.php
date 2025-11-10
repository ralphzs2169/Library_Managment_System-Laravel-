<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class SettingsService
{
    public function getAllSettings()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        return $settings;
    }

    public function updateSettings(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $settings = [
                'borrowing.max_books_per_student' => $request->input('max_books_per_student'),
                'borrowing.max_books_per_teacher' => $request->input('max_books_per_teacher'),
                'borrowing.borrow_duration' => $request->input('borrow_duration'),
                'penalty.rate_per_day' => $request->input('rate_per_day'),
                'penalty.max_amount' => $request->input('max_amount'),
                'penalty.lost_fee_multiplier' => $request->input('lost_fee_multiplier'),
                'penalty.damaged_fee_multiplier' => $request->input('damaged_fee_multiplier'),
                'notifications.reminder_days_before_due' => $request->input('reminder_days_before_due'),
                'notifications.enable_borrower_notifications' => $request->has('enable_borrower_notifications') ? 1 : 0,
                'notifications.show_overdue_notifications' => $request->has('show_overdue_notifications') ? 1 : 0,
            ];

            foreach ($settings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => $value, 'updated_at' => now()]
                );
            }

            ActivityLog::create([
                'action' => ActivityLog::ACTION_UPDATED,
                'details' => 'Updated system settings',
                'entity_type' => 'Settings',
                'entity_id' => 0, // Using 0 to indicate system-wide settings
                'user_id' => $request->user()->id,
            ]);
        });
    }
}
