<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetUserInteractionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $last = $request->user()->last_interaction;

        if (now()->diffInMinutes($last)>3*60)
        {
            Auth::user()->tokens()->delete();
            abort(401);
        }

        $request->user()->update([
            'last_interaction' => now()
        ]);

        return $next($request);
    }
}
