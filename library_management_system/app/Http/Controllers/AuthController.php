<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.login');
    }

    public function showSignupForm()
    {
        $departments = Department::all();
        return view('pages.signup', ['departments' => $departments]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        try {
            $user = User::where('username', $credentials['username'])->first();
        } catch (\Exception $e) {
            return $this->jsonResponse('error', 'Something went wrong', 500);
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => 'Username not found',
            ]);
        }

        if (Hash::check($credentials['password'], $user->password)) {
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            try {
                Auth::login($user);
                $foundUser = Auth::user();

                switch ($foundUser->role) {
                    case 'librarian':
                        $redirectUrl = 'librarian/section/dashboard';
                        break;
                    case 'staff':
                        $redirectUrl = 'staff/dashboard';
                        break;
                    case 'teacher':
                        $redirectUrl = 'teacher/dashboard';
                        break;
                    case 'student':
                        $redirectUrl = 'student/dashboard';
                        break;
                    default:
                        $redirectUrl = '/';
                        break;
                }

                return $this->jsonResponse('success', 'Login successful', 200, [$redirectUrl]);
            } catch (\Exception $e) {
                return $this->jsonResponse('error', 'Something went wrong', 500);
            }
        }

        throw ValidationException::withMessages([
            'password' => 'The password is incorrect',
        ]);
    }

    public function signup(Request $request)
    {
        $rules = [
            // Basic info
            'firstname' => 'required|string|max:45',
            'lastname' => 'required|string|max:45',
            'middle_initial' => 'nullable|string|max:1',

            // Contact info
            'email' => 'required|string|email|max:255|unique:users',
            'contact_number' => [
                'nullable',
                'string',
                'max:13',
                'regex:/^(09\d{9}|\+639\d{9})$/',
            ],

            // Authentication
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:8|confirmed',

            // School Information
            'role' => 'required|in:teacher,student'
        ];

        // Merge role-specific rules
        if ($request->role === 'student') {
            $rules = array_merge($rules, [
                'id_number' => 'required|string|unique:students,student_number',
                'year_level' => 'required|integer|min:1|max:4',
                'department' => 'required|exists:departments,id'
            ]);
        } elseif ($request->role === 'teacher') {
            $rules = array_merge($rules, [
                'id_number' => 'required|string|unique:teachers,employee_number',
                'department' => 'required|exists:departments,id'
            ]);
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'middle_initial' => $request->middle_initial,
                'username' => $request->username,
                'contact_number' => $request->contact_number,
                'role' => 'staff',
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($request->role === 'student') {
                Student::create([
                    'student_number' => $request->id_number,
                    'year_level' => $request->year_level,
                    'department_id' => $request->department,
                    'user_id' => $user->id
                ]);
            } else if ($request->role === 'teacher') {
                Teacher::create([
                    'employee_number' => $request->id_number,
                    'department_id' => $request->department,
                    'user_id' => $user->id
                ]);
            }

            //Save user and profile data
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully'
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during registration: ' . $e->getMessage()
            ], 500);
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
