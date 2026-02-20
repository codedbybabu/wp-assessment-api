<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'wp_users';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    const COL_ID = 'ID';
    const COL_USER_LOGIN = 'user_login';
    const COL_USER_PASS = 'user_pass';
    const COL_USER_NICENAME = 'user_nicename';
    const COL_USER_EMAIL = 'user_email';
    const COL_USER_URL = 'user_url';
    const COL_USER_REGISTERED = 'user_registered';
    const COL_DISPLAY_NAME = 'display_name';

    protected $fillable = [
        self::COL_USER_LOGIN,
        self::COL_USER_PASS,
        self::COL_USER_NICENAME,
        self::COL_USER_EMAIL,
        self::COL_USER_URL,
        self::COL_DISPLAY_NAME
    ];

    protected $hidden = [
        self::COL_USER_PASS,
    ];

    protected $casts = [
        self::COL_USER_REGISTERED => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->{ self::COL_USER_PASS};
    }

    public function getAuthIdentifierName()
    {
        return self::COL_ID;
    }

    public function meta()
    {
        return $this->hasMany(UserMeta::class , 'user_id', self::COL_ID);
    }

    public function orders()
    {
        return $this->hasMany(Order::class , 'post_author', self::COL_ID)
            ->where('post_type', Constants::POST_TYPE_SHOP_ORDER);
    }
    public function getRoleAttribute()
    {
        try {
            $capabilities = $this->meta()
                ->where('meta_key', Constants::USER_META_CAPABILITIES)
                ->first();

            // If not found with wp_ prefix, try without it
            if (!$capabilities) {
                $capabilities = $this->meta()
                    ->where('meta_key', 'capabilities')
                    ->first();
            }

            if (!$capabilities) {
                return Constants::USER_ROLE_CUSTOMER;
            }

            $metaValue = $capabilities->meta_value;

            // WordPress sometimes stores meta with extra slashes
            if (is_string($metaValue)) {
                $metaValue = stripslashes($metaValue);
            }

            $roles = @unserialize($metaValue);

            if (!is_array($roles)) {
                return Constants::USER_ROLE_CUSTOMER;
            }

            // Check roles in order of priority
            if (isset($roles[Constants::USER_ROLE_ADMIN])) {
                return Constants::USER_ROLE_ADMIN;
            }
            if (isset($roles[Constants::USER_ROLE_GOLD])) {
                return Constants::USER_ROLE_GOLD;
            }
            if (isset($roles[Constants::USER_ROLE_SILVER])) {
                return Constants::USER_ROLE_SILVER;
            }

            return Constants::USER_ROLE_CUSTOMER;
        }
        catch (\Exception $e) {
            return Constants::USER_ROLE_CUSTOMER;
        }
    }

    public function isAdmin()
    {
        return $this->role === Constants::USER_ROLE_ADMIN;
    }

    public function isGold()
    {
        return $this->role === Constants::USER_ROLE_GOLD;
    }

    public function isSilver()
    {
        return $this->role === Constants::USER_ROLE_SILVER;
    }

    public function isCustomer()
    {
        return $this->role === Constants::USER_ROLE_CUSTOMER;
    }

    public function getFirstNameAttribute()
    {
        $meta = $this->meta()
            ->where('meta_key', Constants::USER_META_FIRST_NAME)
            ->first();

        return $meta ? $meta->meta_value : null;
    }

    public function getLastNameAttribute()
    {
        $meta = $this->meta()
            ->where('meta_key', Constants::USER_META_LAST_NAME)
            ->first();

        return $meta ? $meta->meta_value : null;
    }

    public function getNicknameAttribute()
    {
        $meta = $this->meta()
            ->where('meta_key', Constants::USER_META_NICKNAME)
            ->first();

        return $meta ? $meta->meta_value : $this->{ self::COL_DISPLAY_NAME};
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'email' => $this->{ self::COL_USER_EMAIL}
        ];
    }
}
