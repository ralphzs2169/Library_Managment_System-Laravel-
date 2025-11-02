<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity')) {
            $query->where('entity_type', $request->entity);
        }

        if ($request->filled('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas(
                'user',
                fn($q) =>
                $q->where(DB::raw("CONCAT(firstname, ' ', lastname)"), 'LIKE', "%{$search}%")
            );
        }

        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'action_az':
                $query->orderBy('action', 'asc');
                break;
            case 'action_za':
                $query->orderBy('action', 'desc');
                break;
            case 'entity_az':
                $query->orderBy('entity_type', 'asc');
                break;
            case 'entity_za':
                $query->orderBy('entity_type', 'desc');
                break;
            case 'user_az':
                $query->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
                    ->orderByRaw("CONCAT(users.firstname, ' ', users.lastname) ASC")
                    ->select('activity_logs.*');
                break;
            case 'user_za':
                $query->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
                    ->orderByRaw("CONCAT(users.firstname, ' ', users.lastname) DESC")
                    ->select('activity_logs.*');
            default:
                $query->orderBy('created_at', 'desc');
        }

        $activity_logs = $query->paginate(10)->withQueryString();
        $activity_logs->load('user');

        return view('pages.librarian.activity-logs', compact('activity_logs'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $activityLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityLog $activityLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityLog $activityLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityLog $activityLog)
    {
        //
    }
}
