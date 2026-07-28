<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated. Please log in.'], 401);
        }
        if (session('role') !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Admin access required.'], 403);
        }
        return $next($request);
    }
}
