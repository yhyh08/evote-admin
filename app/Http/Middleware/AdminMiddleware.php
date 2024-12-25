<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role_id != 1) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Only administrators can access this area.');
        }

        return $next($request);
    }
}
