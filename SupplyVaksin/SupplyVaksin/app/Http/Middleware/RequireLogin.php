<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated. Please log in.'], 401);
        }
        return $next($request);
    }
}
