<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Helpers\Constants;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function attemptLogin($credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !$this->userRepository->validateCredentials($user, $credentials['password'])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $token = JWTAuth::fromUser($user);
        auth('web')->login($user);
        $this->userRepository->updateLastLogin($user->ID);

        return [
            'token' => $token,
            'user' => $this->formatUserResponse($user)
        ];
    }

    public function refreshToken()
    {
        return JWTAuth::refresh();
    }

    public function logout()
    {
        $token = JWTAuth::getToken();

        if ($token) {
            JWTAuth::invalidate($token);
        }

        return true;
    }



    public function getUserFromToken()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return null;
            }

            return $this->formatUserResponse($user);
        }
        catch (\Exception $e) {
            return null;
        }
    }

    protected function formatUserResponse(User $user)
    {
        Log::info($user);
        return [
            'id' => $user->ID,
            'name' => $user->display_name,
            'email' => $user->user_email,
            'role' => $user->role,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'nickname' => $user->nickname
        ];
    }

    public function createToken(User $user)
    {
        return JWTAuth::fromUser($user);
    }
}
