<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\Constants;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], Constants::HTTP_UNAUTHORIZED);
        }

        // Add auth user to request for easy access in controllers
        $request->merge(['auth_user' => $user]);

        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        // We need to implement a hasRole method in User model or check here
        // Based on our User model refactoring, we have role attribute

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Forbidden: Insufficient role'
        ], Constants::HTTP_FORBIDDEN);
    }
}
