<?php

namespace App\Models;

use App\Helpers\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'wp_posts';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function ($query) {
            $query->where('post_type', Constants::POST_TYPE_SHOP_ORDER);
        });
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'post_author', User::COL_ID);
    }

    public function meta()
    {
        return $this->hasMany(PostMeta::class, 'post_id', 'ID');
    }

    public function getMetaValue($key, $default = null)
    {
        $meta = $this->meta()->where('meta_key', $key)->first();

        return $meta ? $meta->meta_value : $default;
    }
    
    public function getCustomerIdAttribute()
    {
        return $this->getMetaValue(Constants::ORDER_META_CUSTOMER_USER);
    }

    public function getOrderTotalAttribute()
    {
        return $this->getMetaValue(Constants::ORDER_META_ORDER_TOTAL, 0);
    }

    public function getOrderKeyAttribute()
    {
        return $this->getMetaValue(Constants::ORDER_META_ORDER_KEY);
    }

    public function getBillingDetailsAttribute()
    {
        return [
            'first_name' => $this->getMetaValue(Constants::ORDER_META_BILLING_FIRST_NAME),
            'last_name' => $this->getMetaValue(Constants::ORDER_META_BILLING_LAST_NAME),
            'email' => $this->getMetaValue(Constants::ORDER_META_BILLING_EMAIL),
            'phone' => $this->getMetaValue(Constants::ORDER_META_BILLING_PHONE)
        ];
    }

    public function isCompleted()
    {
        return $this->post_status === Constants::ORDER_STATUS_COMPLETED;
    }

    public function isProcessing()
    {
        return $this->post_status === Constants::ORDER_STATUS_PROCESSING;
    }

    public function isPending()
    {
        return $this->post_status === Constants::ORDER_STATUS_PENDING;
    }
}
