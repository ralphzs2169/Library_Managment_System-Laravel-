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

    public function detectChanges(Request $request)
    {
        $currentSettings = $this->getAllSettings();
        
        $fields = [
            'max_books_per_student' => 'borrowing.max_books_per_student',
            'max_books_per_teacher' => 'borrowing.max_books_per_teacher',

            'student_duration' => 'borrowing.student_duration',
            'teacher_duration' => 'borrowing.teacher_duration',

            'student_renewal_limit' => 'renewing.student_renewal_limit',
            'teacher_renewal_limit' => 'renewing.teacher_renewal_limit',

            'renewing_student_duration' => 'renewing.student_duration',
            'renewing_teacher_duration' => 'renewing.teacher_duration',

            'student_min_days_before_renewal' => 'renewing.student_min_days_before_renewal',
            'teacher_min_days_before_renewal' => 'renewing.teacher_min_days_before_renewal',
            
            'rate_per_day' => 'penalty.rate_per_day',
            'max_amount' => 'penalty.max_amount',
            'lost_fee_multiplier' => 'penalty.lost_fee_multiplier',
            'damaged_fee_multiplier' => 'penalty.damaged_fee_multiplier',
            'reminder_days_before_due' => 'notifications.reminder_days_before_due',

            'reservation_student_pickup_window_days' => 'reservation.student_pickup_window_days',
            'reservation_teacher_pickup_window_days' => 'reservation.teacher_pickup_window_days',
            'reservation_queue_max_length' => 'reservation.queue_max_length',
            'reservation_student_max_pending_reservations' => 'reservation.student_max_pending_reservations',
            'reservation_teacher_max_pending_reservations' => 'reservation.teacher_max_pending_reservations',
        ];

        $changes = [];

        foreach ($fields as $inputKey => $dbKey) {
            $newValue = $request->input($inputKey);
            $oldValue = $currentSettings[$dbKey] ?? null;

            // Normalize values for comparison
            $normalizedOld = $this->normalizeValue($oldValue);
            $normalizedNew = $this->normalizeValue($newValue);

            if ($normalizedOld !== $normalizedNew) {
                $changes[$inputKey] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        // Check checkbox fields separately
        $checkboxFields = [
            'enable_borrower_notifications' => 'notifications.enable_borrower_notifications',
            'show_overdue_notifications' => 'notifications.show_overdue_notifications',
        ];

        foreach ($checkboxFields as $inputKey => $dbKey) {
            $newValue = $request->has($inputKey) ? 1 : 0;
            $oldValue = (int) ($currentSettings[$dbKey] ?? 0);

            if ($oldValue !== $newValue) {
                $changes[$inputKey] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }

    protected function normalizeValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return trim($value);
    }

    public function updateSettings(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $settings = [
                'borrowing.max_books_per_student' => $request->input('max_books_per_student'),
                'borrowing.max_books_per_teacher' => $request->input('max_books_per_teacher'),
                'borrowing.student_duration' => $request->input('student_duration'),
                'borrowing.teacher_duration' => $request->input('teacher_duration'),
                'renewing.student_renewal_limit' => $request->input('student_renewal_limit'),
                'renewing.teacher_renewal_limit' => $request->input('teacher_renewal_limit'),
                'renewing.student_duration' => $request->input('renewing_student_duration'),
                'renewing.teacher_duration' => $request->input('renewing_teacher_duration'),
                'renewing.student_min_days_before_renewal' => $request->input('student_min_days_before_renewal'),
                'renewing.teacher_min_days_before_renewal' => $request->input('teacher_min_days_before_renewal'),
                'penalty.rate_per_day' => $request->input('rate_per_day'),
                'penalty.max_amount' => $request->input('max_amount'),
                'penalty.lost_fee_multiplier' => $request->input('lost_fee_multiplier'),
                'penalty.damaged_fee_multiplier' => $request->input('damaged_fee_multiplier'),
                'notifications.reminder_days_before_due' => $request->input('reminder_days_before_due'),
                'notifications.enable_borrower_notifications' => $request->has('enable_borrower_notifications') ? 1 : 0,
                'notifications.show_overdue_notifications' => $request->has('show_overdue_notifications') ? 1 : 0,
                'reservation.student_pickup_window_days' => $request->input('reservation_student_pickup_window_days'),
                'reservation.teacher_pickup_window_days' => $request->input('reservation_teacher_pickup_window_days'),
                'reservation.queue_max_length' => $request->input('reservation_queue_max_length'),
                'reservation.student_max_pending_reservations' => $request->input('reservation_student_max_pending_reservations'),
                'reservation.teacher_max_pending_reservations' => $request->input('reservation_teacher_max_pending_reservations'),
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
