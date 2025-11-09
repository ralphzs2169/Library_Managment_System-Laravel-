<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SemesterService;
use App\Models\Semester;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SemesterController extends Controller
{
    protected $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    public function index()
    {
        $semesters = $this->semesterService->getAllSemesters();
        $activeSemester = $this->semesterService->getActiveSemester();
        
        return view('pages.librarian.semester-management', [
            'semesters' => $semesters,
            'activeSemester' => $activeSemester
        ]);
    }

    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Create form accessed successfully'
        ]);
    }

    public function store(Request $request)
    {
        if ($request->validate_only) {
            $request->validate([
                'semester_name' => 'required|string|max:255|unique:semesters,name',
                'semester_start' => 'required|date',
                'semester_end' => 'required|date|after:semester_start',
            ]);

            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $semester = $this->semesterService->storeSemester($request);

            return $this->jsonResponse('success', 'Semester created successfully', 201, ['semester' => $semester]);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Failed to create semester: ' . $e->getMessage(), 500);
        }
    }
    
    public function activate(Request $request, $id)
    {
        if ($request->validate_only) {

            $eligibleForActivation = $this->semesterService->isSemesterEligibleForActivation($id);
            if (!$eligibleForActivation) {
                return $this->jsonResponse('invalid', 'Another active semester exists', 422);
            }
        
            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $semester = $this->semesterService->activateSemester($id, $request);

            return $this->jsonResponse('success', 'Semester activated successfully', 200, ['semester' => $semester]);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Failed to activate semester: ' . $e->getMessage(), 500);
        }
    }
    
    public function edit($id)
    {
        try {
            $semester = Semester::findOrFail($id);
            
            // Only allow editing inactive semesters
            if ($semester->status === 'active') {
                return $this->jsonResponse('error', 'Cannot edit active semester', 403);
            }
            
            return $this->jsonResponse('success', 'Semester retrieved successfully', 200, ['semester' => $semester]);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Semester not found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->validate_only) {
            $request->validate([
                'semester_name' => 'required|string|max:255|unique:semesters,name,' . $id,
                'semester_start' => 'required|date',
                'semester_end' => 'required|date|after:semester_start',
            ]);

            // Check for changes
            $result = $this->semesterService->updateSemester($id, $request);
            
            if (isset($result['status']) && $result['status'] === 'unchanged') {
                return $this->jsonResponse('unchanged', $result['message'], 200);
            }

            return $this->jsonResponse('valid', 'Validation passed', 200);
        }

        try {
            $semester = $this->semesterService->updateSemester($id, $request);

            return $this->jsonResponse('success', 'Semester updated successfully', 200, ['semester' => $semester]);
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Failed to update semester: ' . $e->getMessage(), 500);
        }
    }
}
