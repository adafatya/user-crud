<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if ($token == "") {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized, please log in first!',
                'data' => [],
            ]);
        }

        $user = auth()->user();
        if ($user == null) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized, invalid token!',
                'data' => [],
            ]);
        }

        return $next($request);
    }
}
