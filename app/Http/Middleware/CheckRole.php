<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in AND has any of the required roles
        if (!auth()->check() || !auth()->user()->hasAnyRole($roles)) {
            // Redirect or abort if they don't have access
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
