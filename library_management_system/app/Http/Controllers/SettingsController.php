<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = $this->settingsService->getAllSettings();
        return view('pages.librarian.settings', compact('settings'));
    }

    public function allSettings()
    {
        $settings = $this->settingsService->getAllSettings();
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($request->validate_only) {
            $request->validate([
                'max_books_per_student' => 'required|integer|min:1|max:10',
                'max_books_per_teacher' => 'required|integer|min:1|max:20',

                'student_duration' => 'required|integer|min:1|max:90',
                'teacher_duration' => 'required|integer|min:1|max:90',

                'student_renewal_limit' => 'required|integer|min:1|max:90',
                'teacher_renewal_limit' => 'required|integer|min:1|max:90',

                'renewing_student_duration' => 'required|integer|min:1|max:90',
                'renewing_teacher_duration' => 'required|integer|min:1|max:90',

                'student_min_days_before_renewal' => 'required|integer|min:1|max:90',
                'teacher_min_days_before_renewal' => 'required|integer|min:1|max:90',

                'rate_per_day' => 'required|numeric|min:0|max:100',
                'max_amount' => 'required|numeric|min:0',
                'lost_fee_multiplier' => 'required|numeric|min:0|max:5',
                'damaged_fee_multiplier' => 'required|numeric|min:0|max:5',
                'reminder_days_before_due' => 'required|integer|min:1|max:14',
            ], [
                'max_books_per_student.required' => 'The max books per student field is required.',
                'max_books_per_teacher.required' => 'The max books per teacher field is required.',
                
                'student_duration.required' => 'The student borrow duration field is required.',
                'teacher_duration.required' => 'The teacher borrow duration field is required.',
                
                'student_renewal_limit.required' => 'The student renewal limit field is required.',
                'teacher_renewal_limit.required' => 'The teacher renewal limit field is required.',
                
                'renewing_student_duration.required' => 'The student renewal duration field is required.',
                'renewing_teacher_duration.required' => 'The teacher renewal duration field is required.',
                
                'student_min_days_before_renewal.required' => 'The student min days before renewal field is required.',
                'teacher_min_days_before_renewal.required' => 'The teacher min days before renewal field is required.',
                
                'rate_per_day.required' => 'The penalty rate per day field is required.',
                'max_amount.required' => 'The penalty max amount field is required.',
                'lost_fee_multiplier.required' => 'The lost book multiplier field is required.',
                'damaged_fee_multiplier.required' => 'The damaged book multiplier field is required.',
                'reminder_days_before_due.required' => 'The reminder days field is required.',
            ]);

            // Check for changes
            $changes = $this->settingsService->detectChanges($request);
            
            if (empty($changes)) {
                return $this->jsonResponse('unchanged', 'No changes detected', 200);
            }

            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $this->settingsService->updateSettings($request);
            return $this->jsonResponse('success', 'Settings updated successfully', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Failed to update settings: ' . $e->getMessage(), 500);
        }
    }
}
