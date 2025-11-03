<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Show librarian dashboard.
     */
    public function librarianDashboard(Request $request)
    {
        // Return the librarian dashboard view. Adjust view name if your project uses a different path.
        return view('pages.librarian.dashboard');
    }

    public function staffDashboard(Request $request)
    {
        $query = User::with(['students.department', 'teachers.department'])
            ->whereIn('role', ['student', 'teacher']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('middle_initial', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"])
                  ->orWhereHas('students', function ($q) use ($search) {
                      $q->where('student_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teachers', function ($q) use ($search) {
                      $q->where('employee_number', 'like', "%{$search}%");
                  });
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('library_status', $request->status);
        }

        $users = $query->paginate(20)->withQueryString();
        
        // If AJAX request, return only the table partial
        if ($request->ajax()) {
            return view('partials.staff.members-table', compact('users'))->render();
        }

        return view('pages.staff.dashboard', compact('users'));
    }
}
