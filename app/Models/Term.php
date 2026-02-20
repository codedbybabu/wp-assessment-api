<?php

namespace App\Models;

use App\Helpers\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

     protected $table = 'wp_terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;

    const COL_TERM_ID = 'term_id';
    const COL_NAME = 'name';
    const COL_SLUG = 'slug';
    const COL_TERM_GROUP = 'term_group';

    protected $fillable = [
        self::COL_NAME,
        self::COL_SLUG,
        self::COL_TERM_GROUP
    ];

    public function taxonomy()
    {
        return $this->hasOne(TermTaxonomy::class, 'term_id', self::COL_TERM_ID);
    }

    public function meta()
    {
        return $this->hasMany(TermMeta::class, 'term_id', self::COL_TERM_ID);
    }

    public function getMetaValue($key, $default = null)
    {
        $meta = $this->meta()->where('meta_key', $key)->first();
        return $meta ? $meta->meta_value : $default;
    }

    public function getVisibilityAttribute()
    {
        return $this->getMetaValue(Constants::TERM_META_VISIBILITY, Constants::CATEGORY_VISIBILITY_PUBLIC);
    }

    public function isPublic()
    {
        return $this->visibility === Constants::CATEGORY_VISIBILITY_PUBLIC;
    }

    public function isProtected()
    {
        return $this->visibility === Constants::CATEGORY_VISIBILITY_PROTECTED;
    }

    public function products()
    {
        return $this->belongsToMany(
            Post::class,
            'wp_term_relationships',
            'term_taxonomy_id',
            'object_id'
        )->where('post_type', Constants::POST_TYPE_PRODUCT);
    }
}
