<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckHRRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== 'hr' && Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
