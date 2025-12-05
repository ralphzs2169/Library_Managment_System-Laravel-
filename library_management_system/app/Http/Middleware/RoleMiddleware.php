<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        $user = $request->user();

        // Support multiple roles: 'role:librarian,staff'
        $roleList = explode(',', $roles);

        if (!$user || !in_array($user->role, $roleList)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
