<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if Laravel maintenance mode is enabled
        if (app()->isDownForMaintenance()) {
            // Allow access to specific routes during maintenance
            $allowedRoutes = [
                'maintenance',
                'admin.maintenance',
                'api.health',
                'login',
                'logout'
            ];

            $currentRoute = $request->route() ? $request->route()->getName() : null;
            
            // Allow admin users to access the system during maintenance
            if (auth()->check() && auth()->user()->hasRole(['super_admin', 'admin'])) {
                return $next($request);
            }

            // Allow specific routes
            if (in_array($currentRoute, $allowedRoutes)) {
                return $next($request);
            }

            // Allow login attempts during maintenance
            if ($request->is('login') || $request->is('auth/login')) {
                return $next($request);
            }

            // Return simple 503 error for all other requests
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Service Unavailable',
                    'message' => 'The EMS system is currently under maintenance. Please try again later.',
                    'status' => 503
                ], 503);
            }

            // Return simple 503 HTML response
            return response('<h1>503 Service Unavailable</h1><p>The EMS system is currently under maintenance. Please try again later.</p>', 503);
        }

        return $next($request);
    }
}
