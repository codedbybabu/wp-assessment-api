<?php

namespace App\Helpers;

class Constants
{
    // User Roles
    const USER_ROLE_ADMIN = 'administrator';
    const USER_ROLE_CUSTOMER = 'customer';
    const USER_ROLE_SILVER = 'silver';
    const USER_ROLE_GOLD = 'gold';

    // Database Tables
    const TABLE_USERS = 'wp_users';
    const TABLE_USERMETA = 'wp_usermeta';
    const TABLE_POSTS = 'wp_posts';
    const TABLE_POSTMETA = 'wp_postmeta';
    const TABLE_TERMS = 'wp_terms';
    const TABLE_TERMMETA = 'wp_termmeta';
    const TABLE_TERM_TAXONOMY = 'wp_term_taxonomy';
    const TABLE_TERM_RELATIONSHIPS = 'wp_term_relationships';
    const TABLE_OPTIONS = 'wp_options';

    // User Meta Keys
    const USER_META_CAPABILITIES = 'wp_capabilities';
    const USER_META_FIRST_NAME = 'first_name';
    const USER_META_LAST_NAME = 'last_name';
    const USER_META_NICKNAME = 'nickname';

    // Post Types
    const POST_TYPE_PRODUCT = 'product';
    const POST_TYPE_PRODUCT_VARIATION = 'product_variation';
    const POST_TYPE_SHOP_ORDER = 'shop_order';
    const POST_TYPE_PAGE = 'page';
    const POST_TYPE_POST = 'post';

    // Post Status
    const POST_STATUS_PUBLISH = 'publish';
    const POST_STATUS_DRAFT = 'draft';
    const POST_STATUS_PRIVATE = 'private';

    // Order Status
    const ORDER_STATUS_COMPLETED = 'wc-completed';
    const ORDER_STATUS_PROCESSING = 'wc-processing';
    const ORDER_STATUS_PENDING = 'wc-pending';
    const ORDER_STATUS_CANCELLED = 'wc-cancelled';
    const ORDER_STATUS_REFUNDED = 'wc-refunded';
    const ORDER_STATUS_FAILED = 'wc-failed';

    // Taxonomy Types
    const TAXONOMY_PRODUCT_CAT = 'product_cat';
    const TAXONOMY_PRODUCT_TYPE = 'product_type';
    const TAXONOMY_PRODUCT_VISIBILITY = 'product_visibility';
    const TAXONOMY_CATEGORY = 'category';

    // Term Meta Keys
    const TERM_META_VISIBILITY = 'category_visibility';
    const TERM_META_ORDER = 'order';
    const TERM_META_THUMBNAIL_ID = 'thumbnail_id';

    // Terms
    const TERM_FEATURED = 'featured';

    // Cache Prefixes
    const CACHE_KEY_PRODUCT_PREFIX = 'product_';

    // Category Visibility
    const CATEGORY_VISIBILITY_PUBLIC = 'public';
    const CATEGORY_VISIBILITY_PROTECTED = 'protected';

    // Product Meta Keys
    const PRODUCT_META_REGULAR_PRICE = '_regular_price';
    const PRODUCT_META_PRICE = '_price';
    const PRODUCT_META_CUSTOMER_PRICE = '_customer_price';
    const PRODUCT_META_SILVER_PRICE = '_silver_price';
    const PRODUCT_META_GOLD_PRICE = '_gold_price';
    const PRODUCT_META_STOCK = '_stock';
    const PRODUCT_META_STOCK_STATUS = '_stock_status';
    const PRODUCT_META_MANAGE_STOCK = '_manage_stock';
    const PRODUCT_META_SKU = '_sku';
    const PRODUCT_META_TAX_STATUS = '_tax_status';

    // Stock Status
    const STOCK_STATUS_INSTOCK = 'instock';
    const STOCK_STATUS_OUTOFSTOCK = 'outofstock';
    const STOCK_STATUS_ONBACKORDER = 'onbackorder';

    // Order Meta Keys
    const ORDER_META_CUSTOMER_USER = '_customer_user';
    const ORDER_META_ORDER_KEY = '_order_key';
    const ORDER_META_ORDER_TOTAL = '_order_total';
    const ORDER_META_ORDER_TAX = '_order_tax';
    const ORDER_META_BILLING_FIRST_NAME = '_billing_first_name';
    const ORDER_META_BILLING_LAST_NAME = '_billing_last_name';
    const ORDER_META_BILLING_EMAIL = '_billing_email';
    const ORDER_META_BILLING_PHONE = '_billing_phone';

    // Pagination
    const PAGINATE_PER_PAGE = 12;

    // HTTP Status Codes
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_SERVER_ERROR = 500;

    // Cache Keys
    const CACHE_PRODUCTS_KEY = 'products';
    const CACHE_CATEGORIES_KEY = 'categories';
    const CACHE_USER_ROLE_KEY = 'user_role';

    // Cache TTL (seconds)
    const CACHE_TTL_ONE_HOUR = 3600;
    const CACHE_TTL_ONE_DAY = 86400;
}
