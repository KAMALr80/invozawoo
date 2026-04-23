<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckLogisticsStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logisticsEnabled = Cache::get('logistics_system_enabled', true);

        if (!$logisticsEnabled) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Service Unavailable',
                    'message' => 'The logistics system is currently offline.'
                ], 503);
            }
            
            abort(403, 'The logistics system is currently offline. Please contact the administrator.');
        }

        return $next($request);
    }
}
