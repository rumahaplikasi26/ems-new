<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session or use default
        $locale = Session::get('locale', config('app.locale'));
        
        // Validate locale
        $supportedLocales = ['id', 'en'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale');
        }
        
        // Set application locale
        App::setLocale($locale);
        
        return $next($request);
    }
}
