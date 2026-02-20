<?php

namespace App\Services;

use App\Models\Post;
use App\Helpers\Constants;

class PricingService
{
    public function getPriceForRole(Post $product, $role = null)
    {
        if (!$role) {
            return $this->getDefaultPrice($product);
        }

        switch ($role) {
            case Constants::USER_ROLE_GOLD:
                return $this->getGoldPrice($product);
            case Constants::USER_ROLE_SILVER:
                return $this->getSilverPrice($product);
            case Constants::USER_ROLE_CUSTOMER:
            default:
                return $this->getCustomerPrice($product);
        }
    }

    protected function getDefaultPrice(Post $product)
    {
        $price = $product->getMetaValue(Constants::PRODUCT_META_CUSTOMER_PRICE);

        if (!$price) {
            $price = $product->getMetaValue(Constants::PRODUCT_META_REGULAR_PRICE);
        }

        if (!$price) {
            $price = $product->getMetaValue(Constants::PRODUCT_META_PRICE);
        }

        return $price ? floatval($price) : 0;
    }

    protected function getCustomerPrice(Post $product)
    {
        $price = $product->getMetaValue(Constants::PRODUCT_META_CUSTOMER_PRICE);

        if (!$price) {
            $price = $product->getMetaValue(Constants::PRODUCT_META_REGULAR_PRICE);
        }

        return $price ? floatval($price) : 0;
    }

    protected function getSilverPrice(Post $product)
    {
        $price = $product->getMetaValue(Constants::PRODUCT_META_SILVER_PRICE);

        if (!$price) {
            $price = $this->getCustomerPrice($product);
        }

        return $price ? floatval($price) : 0;
    }

    protected function getGoldPrice(Post $product)
    {
        $price = $product->getMetaValue(Constants::PRODUCT_META_GOLD_PRICE);

        if (!$price) {
            $price = $this->getCustomerPrice($product);
        }

        return $price ? floatval($price) : 0;
    }

    public function formatPrice($price, $currency = '$')
    {
        return $currency . number_format($price, 2);
    }

    public function calculateDiscount($originalPrice, $discountedPrice)
    {
        if ($originalPrice <= 0) {
            return 0;
        }

        return round((($originalPrice - $discountedPrice) / $originalPrice) * 100);
    }
}
