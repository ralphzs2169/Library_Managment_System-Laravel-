<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterService
{
    public function storeSemester(Request $data)
    {
        return DB::transaction(function () use ($data) {
            // Create the semester (inactive by default)
            $semester = Semester::create([
                'name' => $data->semester_name,
                'start_date' => $data->semester_start,
                'end_date' => $data->semester_end,
                'status' => 'inactive'
            ]);

            ActivityLog::create([
                'action' => 'Create',
                'details' => 'Created semester: ' . $semester->name,
                'entity_type' => 'Semester',
                'entity_id' => $semester->id,
                'user_id' => $data->user()->id,
            ]);

            return $semester;
        });
    }

    public function isSemesterEligibleForActivation(int $semesterId)
    {
        $semester = Semester::findOrFail($semesterId);
        
        // Already active - not eligible
        if ($semester->status === 'active') {
            return false;
        }

        // Check if there's any active semester
        $hasActiveSemester = Semester::where('status', 'active')->exists();
        
        // Not eligible if there's already an active semester
        return !$hasActiveSemester;
    }

    public function activateSemester(int $semesterId, Request $request)
    {
        return DB::transaction(function () use ($semesterId, $request) {
            // Find the semester first
            $semester = Semester::findOrFail($semesterId);
            
            // Deactivate all other semesters
            Semester::where('status', 'active')
                ->where('id', '!=', $semesterId)
                ->update(['status' => 'inactive']);

            // Activate the selected semester
            $semester->status = 'active';
            $semester->save();

            ActivityLog::create([
                'action' => 'Activate',
                'details' => 'Activated semester: ' . $semester->name,
                'entity_type' => 'Semester',
                'entity_id' => $semester->id,
                'user_id' => $request->user()->id,
            ]);

            return $semester->fresh(); // Reload from database
        });
    }

    public function getAllSemesters()
    {
        return Semester::orderBy('start_date', 'desc')->get();
    }

    public function getActiveSemester()
    {
        return Semester::where('status', 'active')->first();
    }

    public function updateSemester(int $semesterId, Request $request)
    {
        return DB::transaction(function () use ($semesterId, $request) {
            $semester = Semester::findOrFail($semesterId);
            
            // Only allow updating inactive semesters
            if ($semester->status === 'active') {
                throw new \Exception('Cannot update active semester');
            }

            // Detect changes if validate_only
            if ($request->boolean('validate_only')) {
                $changes = $this->detectSemesterChanges($request, $semester);
                
                if (empty($changes)) {
                    return ['status' => 'unchanged', 'message' => 'No changes detected.'];
                }
                
                return ['status' => 'success', 'message' => 'Validation passed'];
            }
            
            $semester->update([
                'name' => $request->semester_name,
                'start_date' => $request->semester_start,
                'end_date' => $request->semester_end,
            ]);

            ActivityLog::create([
                'action' => 'Update',
                'details' => 'Updated semester: ' . $semester->name,
                'entity_type' => 'Semester',
                'entity_id' => $semester->id,
                'user_id' => $request->user()->id,
            ]);

            return $semester->fresh();
        });
    }

    protected function detectSemesterChanges(Request $request, Semester $semester)
    {
        $changes = [];

        $normalize = function ($value) {
            if ($value === '' || $value === null) return null;
            return is_string($value) ? trim($value) : $value;
        };

        // Check semester name
        if ($request->has('semester_name')) {
            $oldName = $normalize($semester->name);
            $newName = $normalize($request->semester_name);
            if ($oldName !== $newName) {
                $changes['semester_name'] = ['old' => $semester->name, 'new' => $request->semester_name];
            }
        }

        // Check start date
        if ($request->has('semester_start')) {
            $oldStart = $semester->start_date;
            $newStart = $request->semester_start;
            if ($oldStart !== $newStart) {
                $changes['semester_start'] = ['old' => $oldStart, 'new' => $newStart];
            }
        }

        // Check end date
        if ($request->has('semester_end')) {
            $oldEnd = $semester->end_date;
            $newEnd = $request->semester_end;
            if ($oldEnd !== $newEnd) {
                $changes['semester_end'] = ['old' => $oldEnd, 'new' => $newEnd];
            }
        }

        return $changes;
    }
}