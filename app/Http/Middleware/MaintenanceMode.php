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
        // Check if maintenance mode is enabled
        if (config('app.maintenance_mode', false)) {
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

            // Return maintenance page for all other requests
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Maintenance Mode',
                    'message' => 'The EMS system is currently under maintenance. Please try again later.',
                    'estimated_completion' => config('app.maintenance_eta', '2-3 hours'),
                    'contact_email' => config('app.maintenance_contact_email', 'ems-support@company.com'),
                    'contact_phone' => config('app.maintenance_contact_phone', '+62 21 1234 5678')
                ], 503);
            }

            return response()->view('maintenance', [
                'message' => config('app.maintenance_message', 'We are currently performing scheduled maintenance on the Employee Management System.'),
                'estimated_completion' => config('app.maintenance_eta', '2-3 hours'),
                'contact_email' => config('app.maintenance_contact_email', 'ems-support@company.com'),
                'contact_phone' => config('app.maintenance_contact_phone', '+62 21 1234 5678'),
                'system_name' => 'Employee Management System'
            ], 503);
        }

        return $next($request);
    }
}
