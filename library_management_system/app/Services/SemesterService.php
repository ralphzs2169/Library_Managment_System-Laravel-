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
                'action' => 'created',
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
                'action' => 'activated',
                'details' => 'Activated semester: ' . $semester->name,
                'entity_type' => 'Semester',
                'entity_id' => $semester->id,
                'user_id' => $request->user()->id,
            ]);

            return $semester->fresh(); // Reload from database
        });
    }

    public function getAllSemesters($filters = [])
    {
        $query = Semester::query();

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Apply status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Apply sort filter
        if (!empty($filters['sort'])) {
            $sort = $filters['sort'];
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('start_date', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                default: // newest
                    $query->orderBy('start_date', 'desc');
            }
        } else {
            $query->orderBy('start_date', 'desc'); // default sort
        }

        return $query->paginate(10)->withQueryString();
    }

    public function getActiveSemester()
    {
        $activeSemester = Semester::where('status', 'active')->first();
        return $activeSemester ? $activeSemester : null;
    }

    public static function getActiveSemesterId()
    {
        $activeSemester = Semester::where('status', 'active')->first();
        return $activeSemester ? $activeSemester->id : null;
    }

    public function updateSemester(int $semesterId, Request $request)
    {
        $semester = Semester::findOrFail($semesterId);
        $changes = '';

        if ($request->boolean('validate_only')) {
            // Only allow updating inactive semesters
            if ($semester->status === 'active') {
                throw new \Exception('Cannot update active semester');
            }

            $changes = $this->detectSemesterChanges($request, $semester);
            
            if (empty($changes)) {
                return ['status' => 'unchanged', 'message' => 'No changes detected.'];
            }
            
            return ['status' => 'success', 'message' => 'Validation passed'];
        }

        try {
            return DB::transaction(function () use ($semester, $request) {
                $semester->update([
                    'name' => $request->input('edit_semester_name'),
                    'start_date' => $request->input('edit_semester_start'),
                    'end_date' => $request->input('edit_semester_end'),
                ]);

                ActivityLog::create([
                    'action' => 'updated',
                    'details' => 'Updated semester: ' . $semester->name,
                    'entity_type' => 'Semester',
                    'entity_id' => $semester->id,
                    'user_id' => $request->user()->id,
                ]);

                return $semester->fresh();
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deactivateSemester(int $semesterId, Request $request)
    {
        return DB::transaction(function () use ($semesterId, $request) {
            $semester = Semester::findOrFail($semesterId);
            
            // Only allow deactivating active semesters
            if ($semester->status !== 'active') {
                throw new \Exception('Only active semesters can be deactivated');
            }
            
            // Deactivate the semester
            $semester->status = 'inactive';
            $semester->save();

            ActivityLog::create([
                'action' => 'deactivated',
                'details' => 'Deactivated semester: ' . $semester->name,
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
        if ($request->has('edit_semester_name')) {
            $oldName = $normalize($semester->name);
            $newName = $normalize($request->input('edit_semester_name'));
            if ($oldName !== $newName) {
                $changes['semester_name'] = ['old' => $semester->name, 'new' => $request->input('edit_semester_name')];
            }
        }

        // Check start date
        if ($request->has('edit_semester_start')) {
            $oldStart = $semester->start_date;
            $newStart = $request->input('edit_semester_start');
            if ($oldStart !== $newStart) {
                $changes['semester_start'] = ['old' => $oldStart, 'new' => $newStart];
            }
        }

        // Check end date
        if ($request->has('edit_semester_end')) {
            $oldEnd = $semester->end_date;
            $newEnd = $request->input('edit_semester_end');
            if ($oldEnd !== $newEnd) {
                $changes['semester_end'] = ['old' => $oldEnd, 'new' => $newEnd];
            }
        }

        return $changes;
    }
}