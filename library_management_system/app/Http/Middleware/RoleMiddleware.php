<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Support multiple roles: 'role:librarian,staff' or 'role:librarian', 'role:staff'
        $roleList = [];
        foreach ($roles as $roleArg) {
            foreach (explode(',', $roleArg) as $role) {
                $roleList[] = trim($role);
            }
        }

        if (!$user || !in_array($user->role, $roleList)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
