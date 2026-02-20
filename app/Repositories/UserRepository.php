<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserMeta;
use App\Helpers\Constants;
use Hautelook\Phpass\PasswordHash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail($email)
    {
        return $this->model->where(User::COL_USER_EMAIL, $email)->first();
    }

    public function findById($id)
    {
        $cacheKey = Constants::CACHE_USER_ROLE_KEY . '_' . $id;

        return Cache::remember($cacheKey, Constants::CACHE_TTL_ONE_HOUR, function () use ($id) {
            return $this->model->with(['meta'])->find($id);
        });
    }

    public function updateLastLogin($userId)
    {
        // You could create a last_login meta field
        UserMeta::updateOrCreate(
            [
                'user_id' => $userId,
                'meta_key' => 'last_login'
            ],
            [
                'meta_value' => now()->toDateTimeString()
            ]
        );
    }

    public function validateCredentials($user, $password)
    {
        if (!$user) {
            return false;
        }

        $hasher = new PasswordHash(8, true);

        return $hasher->CheckPassword($password, $user->user_pass);
    }
    public function getUserMeta($userId, $key, $default = null)
    {
        $meta = UserMeta::where('user_id', $userId)
            ->where('meta_key', $key)
            ->first();

        return $meta ? $meta->meta_value : $default;
    }

    public function updateUserMeta($userId, $key, $value)
    {
        return UserMeta::updateOrCreate(
            [
                'user_id' => $userId,
                'meta_key' => $key
            ],
            [
                'meta_value' => $value
            ]
        );
    }

    public function getAllUsersWithRole($role = null, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        $query = $this->model->query();

        if ($role) {
            $query->whereHas('meta', function ($q) use ($role) {
                $q->where('meta_key', Constants::USER_META_CAPABILITIES)
                    ->where('meta_value', 'like', "%{$role}%");
            });
        }

        return $query->with(['meta'])
            ->orderBy('user_registered', 'desc')
            ->paginate($perPage);
    }

    public function getUsersCount()
    {
        return $this->model->count();
    }

    public function getUsersCountByRole()
    {
        $users = $this->model->with(['meta'])->get();

        $stats = [
            Constants::USER_ROLE_ADMIN => 0,
            Constants::USER_ROLE_GOLD => 0,
            Constants::USER_ROLE_SILVER => 0,
            Constants::USER_ROLE_CUSTOMER => 0
        ];

        foreach ($users as $user) {
            $role = $user->role;
            if (isset($stats[$role])) {
                $stats[$role]++;
            }
        }

        return $stats;
    }

    public function searchUsers($searchTerm, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        return $this->model->where(function ($query) use ($searchTerm) {
            $query->where(User::COL_USER_LOGIN, 'like', "%{$searchTerm}%")
                ->orWhere(User::COL_USER_EMAIL, 'like', "%{$searchTerm}%")
                ->orWhere(User::COL_DISPLAY_NAME, 'like', "%{$searchTerm}%")
                ->orWhere(User::COL_USER_NICENAME, 'like', "%{$searchTerm}%");
        })
            ->with(['meta'])
            ->orderBy('user_registered', 'desc')
            ->paginate($perPage);
    }

    public function clearUserCache($userId)
    {
        Cache::forget(Constants::CACHE_USER_ROLE_KEY . '_' . $userId);
    }
}
