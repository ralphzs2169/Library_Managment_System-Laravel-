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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($request->validate_only) {
            $request->validate([
                'max_books_per_student' => 'required|integer|min:1|max:10',
                'max_books_per_teacher' => 'required|integer|min:1|max:20',
                'borrow_duration' => 'required|integer|min:1|max:90',
                'rate_per_day' => 'required|numeric|min:0|max:100',
                'max_amount' => 'required|numeric|min:0',
                'lost_fee_multiplier' => 'required|numeric|min:0|max:5',
                'damaged_fee_multiplier' => 'required|numeric|min:0|max:5',
                'reminder_days_before_due' => 'required|integer|min:1|max:14',
            ], [
                'max_books_per_student.required' => 'The max books per student field is required.',
                'max_books_per_teacher.required' => 'The max books per teacher field is required.',
                'borrow_duration.required' => 'The borrow duration field is required.',
                'rate_per_day.required' => 'The penalty rate per day field is required.',
                'max_amount.required' => 'The penalty max amount field is required.',
                'lost_fee_multiplier.required' => 'The lost book multiplier field is required.',
                'damaged_fee_multiplier.required' => 'The damaged book multiplier field is required.',
                'reminder_days_before_due.required' => 'The reminder days field is required.',
            ]);

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
