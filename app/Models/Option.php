<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $table = 'wp_options';
    protected $primaryKey = 'option_id';
    public $timestamps = false;

    const COL_OPTION_ID = 'option_id';
    const COL_OPTION_NAME = 'option_name';
    const COL_OPTION_VALUE = 'option_value';
    const COL_AUTOLOAD = 'autoload';

    protected $fillable = [
        self::COL_OPTION_NAME,
        self::COL_OPTION_VALUE,
        self::COL_AUTOLOAD
    ];

    protected $casts = [
        self::COL_OPTION_VALUE => 'array'
    ];

    public static function get($key, $default = null)
    {
        $option = self::where(self::COL_OPTION_NAME, $key)->first();
        return $option ? $option->{self::COL_OPTION_VALUE} : $default;
    }

    public static function set($key, $value, $autoload = 'yes')
    {
        return self::updateOrCreate(
            [self::COL_OPTION_NAME => $key],
            [
                self::COL_OPTION_VALUE => $value,
                self::COL_AUTOLOAD => $autoload
            ]
        );
    }
}
