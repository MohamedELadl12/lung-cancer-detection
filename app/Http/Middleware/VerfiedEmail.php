<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerfiedEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (auth('api')->check() && auth('api')->user()->email_verified_at != null) {
            // If the user is authenticated but their email is not verified, return a 403 response
            return $next($request);
            
        }

        return response()->json([
            'message' => 'Email not verified'
        ], 403);
        
    }
}
