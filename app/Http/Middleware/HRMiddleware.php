<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HRMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $role = Auth::user()->role;

        // ✅ HR OR ADMIN allowed
        if (!in_array($role, ['hr', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
