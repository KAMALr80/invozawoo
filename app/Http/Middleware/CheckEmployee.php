<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class CheckEmployee
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee record not found. Please contact HR to link your account.');
        }

        // Share employee data with all views
        view()->share('currentEmployee', $employee);

        return $next($request);
    }
}
