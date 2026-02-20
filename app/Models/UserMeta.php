<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    use HasFactory;

     protected $table = 'wp_usermeta';
    protected $primaryKey = 'umeta_id';
    public $timestamps = false;

    const COL_UMETA_ID = 'umeta_id';
    const COL_USER_ID = 'user_id';
    const COL_META_KEY = 'meta_key';
    const COL_META_VALUE = 'meta_value';

    protected $fillable = [
        self::COL_USER_ID,
        self::COL_META_KEY,
        self::COL_META_VALUE
    ];

    protected $casts = [
        self::COL_META_VALUE => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, self::COL_USER_ID, User::COL_ID);
    }
}
