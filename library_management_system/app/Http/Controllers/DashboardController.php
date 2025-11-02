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
        $users = User::with([
                'students.department', 
                'teachers.department'   
        ])
        ->whereNotIn('role', ['librarian', 'staff'])    
        ->get();
        
        return view('pages.staff.dashboard', compact('users'));
    }
}
