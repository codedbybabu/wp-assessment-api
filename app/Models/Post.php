<?php

namespace App\Models;

use App\Helpers\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'wp_posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    const COL_ID = 'ID';
    const COL_POST_AUTHOR = 'post_author';
    const COL_POST_DATE = 'post_date';
    const COL_POST_DATE_GMT = 'post_date_gmt';
    const COL_POST_CONTENT = 'post_content';
    const COL_POST_TITLE = 'post_title';
    const COL_POST_EXCERPT = 'post_excerpt';
    const COL_POST_STATUS = 'post_status';
    const COL_POST_NAME = 'post_name';
    const COL_POST_MODIFIED = 'post_modified';
    const COL_POST_MODIFIED_GMT = 'post_modified_gmt';
    const COL_POST_PARENT = 'post_parent';
    const COL_GUID = 'guid';
    const COL_POST_TYPE = 'post_type';

    protected $fillable = [
        self::COL_POST_AUTHOR,
        self::COL_POST_TITLE,
        self::COL_POST_CONTENT,
        self::COL_POST_EXCERPT,
        self::COL_POST_STATUS,
        self::COL_POST_NAME,
        self::COL_POST_TYPE
    ];

    protected $casts = [
        self::COL_POST_DATE => 'datetime',
        self::COL_POST_DATE_GMT => 'datetime',
        self::COL_POST_MODIFIED => 'datetime',
        self::COL_POST_MODIFIED_GMT => 'datetime'
    ];

    public function author()
    {
        return $this->belongsTo(User::class , self::COL_POST_AUTHOR, User::COL_ID);
    }

    public function meta()
    {
        return $this->hasMany(PostMeta::class , 'post_id', self::COL_ID);
    }

    public function parent()
    {
        return $this->belongsTo(Post::class , self::COL_POST_PARENT, self::COL_ID);
    }

    public function children()
    {
        return $this->hasMany(Post::class , self::COL_POST_PARENT, self::COL_ID);
    }

    public function termRelationships()
    {
        return $this->hasMany(TermRelationship::class , 'object_id', self::COL_ID);
    }

    public function categories()
    {
        return $this->belongsToMany(
            Term::class ,
            'wp_term_relationships',
            'object_id',
            'term_taxonomy_id'
        )->whereHas('taxonomy', function ($query) {
            $query->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT);
        });
    }

    public function scopeProduct($query)
    {
        return $query->where(self::COL_POST_TYPE, Constants::POST_TYPE_PRODUCT)
            ->where(self::COL_POST_STATUS, Constants::POST_STATUS_PUBLISH);
    }

    public function scopeProductVariation($query)
    {
        return $query->where(self::COL_POST_TYPE, Constants::POST_TYPE_PRODUCT_VARIATION)
            ->where(self::COL_POST_STATUS, Constants::POST_STATUS_PUBLISH);
    }

    public function scopeShopOrder($query)
    {
        return $query->where(self::COL_POST_TYPE, Constants::POST_TYPE_SHOP_ORDER);
    }

    public function getMetaValue($key, $default = null)
    {
        $meta = $this->meta()->where('meta_key', $key)->first();
        return $meta ? $meta->meta_value : $default;
    }



    public function getStockAttribute()
    {
        return $this->getMetaValue(Constants::PRODUCT_META_STOCK, 0);
    }

    public function getStockStatusAttribute()
    {
        return $this->getMetaValue(Constants::PRODUCT_META_STOCK_STATUS, Constants::STOCK_STATUS_OUTOFSTOCK);
    }

    public function getSkuAttribute()
    {
        return $this->getMetaValue(Constants::PRODUCT_META_SKU);
    }
}
