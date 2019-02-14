<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EditorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!Auth::user()->isEditor() && !Auth::user()->isAdmin()) {
            return response()->json([
                'message' => 'Editor access is required'
            ],401);
        }
        return $next($request);
    }
}
