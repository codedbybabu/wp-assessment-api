<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->attemptLogin($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => $result
            ], Constants::HTTP_OK);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'errors' => $e->errors()
            ], Constants::HTTP_UNPROCESSABLE_ENTITY);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed: ' . $e->getMessage()
            ], Constants::HTTP_SERVER_ERROR);
        }
    }

    public function logout(Request $request)
    {
        Log::info('Logout initiated', [
            'user_id' => auth()->id(),
            'guard' => auth()->getDefaultDriver(),
            'is_json' => $request->wantsJson()
        ]);

        // Session logout
        auth('web')->logout();

        // Session destroy
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Web session cleared');

        // JWT invalidate (optional)
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
                Log::info('JWT token invalidated');
            }
        }
        catch (\Exception $e) {
            Log::error('JWT invalidation failed: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ], Constants::HTTP_OK);
        }

        return redirect('/login');
    }


    public function refresh(Request $request)
    {
        try {
            $token = $this->authService->refreshToken();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'token' => $token
                ]
            ], Constants::HTTP_OK);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token refresh failed'
            ], Constants::HTTP_UNAUTHORIZED);
        }
    }

    public function me(Request $request)
    {
        $user = $this->authService->getUserFromToken();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], Constants::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], Constants::HTTP_OK);
    }
}
