<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if (!Auth::check()) {
            // Redirect to login if not authenticated
            return redirect()->route('admin.login'); 
        }

        return $next($request);
    }
}
