<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    use HasFactory;
    protected $table = 'wp_postmeta';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;

    const COL_META_ID = 'meta_id';
    const COL_POST_ID = 'post_id';
    const COL_META_KEY = 'meta_key';
    const COL_META_VALUE = 'meta_value';

    protected $fillable = [
        self::COL_POST_ID,
        self::COL_META_KEY,
        self::COL_META_VALUE
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, self::COL_POST_ID, Post::COL_ID);
    }
}
