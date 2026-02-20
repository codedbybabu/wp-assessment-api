<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Term;
use App\Models\PostMeta;
use App\Helpers\Constants;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function getShopProducts($userRole = null, $perPage = Constants::PAGINATE_PER_PAGE, $search = null, $category = null)
    {
        $cacheKey = Constants::CACHE_PRODUCTS_KEY . '_shop_' . ($userRole ?? 'guest') . '_page_' . request()->get('page', 1);

        if ($search) {
            $cacheKey .= '_search_' . md5($search);
        }

        if ($category) {
            $cacheKey .= '_cat_' . $category;
        }

        return Cache::remember($cacheKey, Constants::CACHE_TTL_ONE_HOUR, function () use ($userRole, $perPage, $search, $category) {
            $query = $this->model->product();

            // Apply search
            if ($search) {
                $query->where(function ($q) use ($search) {
                            $q->where(Post::COL_POST_TITLE, 'like', "%{$search}%")
                                ->orWhere(Post::COL_POST_CONTENT, 'like', "%{$search}%")
                                ->orWhere(Post::COL_POST_EXCERPT, 'like', "%{$search}%");
                        }
                        );
                    }

                    // Filter by category
                    if ($category && $category !== 'all') {
                        $query->whereHas('categories', function ($q) use ($category) {
                            $q->where('slug', $category);
                        }
                        );
                    }

                    // Filter by category visibility for guests
                    if (!$userRole) {
                        $publicCategoryIds = Term::whereHas('meta', function ($q) {
                            $q->where('meta_key', Constants::TERM_META_VISIBILITY)
                                ->where('meta_value', Constants::CATEGORY_VISIBILITY_PUBLIC);
                        }
                        )->pluck(Term::COL_TERM_ID);

                        $query->whereHas('termRelationships', function ($q) use ($publicCategoryIds) {
                            $q->whereIn('term_taxonomy_id', function ($subQuery) use ($publicCategoryIds) {
                                    $subQuery->select('term_taxonomy_id')
                                        ->from(Constants::TABLE_TERM_TAXONOMY)
                                        ->whereIn('term_id', $publicCategoryIds);
                                }
                                );
                            }
                            );
                        }

                        return $query->with(['meta', 'categories.meta'])
                            ->orderBy('post_date', 'desc')
                            ->paginate($perPage);
                    });
    }

    public function findBySlug($slug)
    {
        return $this->model->product()
            ->where(Post::COL_POST_NAME, $slug)
            ->with(['meta', 'categories.meta', 'children.meta'])
            ->first();
    }

    public function findById($id)
    {
        $cacheKey = Constants::CACHE_KEY_PRODUCT_PREFIX . $id;

        return Cache::remember($cacheKey, Constants::CACHE_TTL_ONE_HOUR, function () use ($id) {
            return $this->model->product()
                ->where(Post::COL_ID, $id)
                ->with(['meta', 'categories.meta', 'children.meta'])
                ->first();
        });
    }

    public function getVariations($productId)
    {
        return $this->model->productVariation()
            ->where(Post::COL_POST_PARENT, $productId)
            ->with('meta')
            ->get();
    }



    public function getRelatedProducts($productId, $categoryIds, $limit = 4)
    {
        return $this->model->product()
            ->where(Post::COL_ID, '!=', $productId)
            ->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('term_id', $categoryIds);
        })
            ->with(['meta', 'categories'])
            ->limit($limit)
            ->get();
    }

    public function getFeaturedProducts($limit = 8)
    {
        return $this->model->product()
            ->whereHas('termRelationships', function ($q) {
            $q->whereIn('term_taxonomy_id', function ($subQuery) {
                    $subQuery->select('term_taxonomy_id')
                        ->from(Constants::TABLE_TERM_TAXONOMY)
                        ->where('taxonomy', Constants::TAXONOMY_PRODUCT_VISIBILITY)
                        ->where('term_id', function ($innerQuery) {
                    $innerQuery->select('term_id')
                        ->from(Constants::TABLE_TERMS)
                        ->where('slug', Constants::TERM_FEATURED);
                }
                );
            }
            );
        })
            ->with(['meta', 'categories'])
            ->limit($limit)
            ->get();
    }

    public function updateStock($productId, $quantity)
    {
        $currentStock = $this->getStock($productId);
        $newStock = max(0, $currentStock - $quantity);

        PostMeta::updateOrCreate(
        [
            'post_id' => $productId,
            'meta_key' => Constants::PRODUCT_META_STOCK
        ],
        [
            'meta_value' => $newStock
        ]
        );

        // Update stock status
        $status = $newStock > 0 ?Constants::STOCK_STATUS_INSTOCK : Constants::STOCK_STATUS_OUTOFSTOCK;

        PostMeta::updateOrCreate(
        [
            'post_id' => $productId,
            'meta_key' => Constants::PRODUCT_META_STOCK_STATUS
        ],
        [
            'meta_value' => $status
        ]
        );

        $this->clearProductCache($productId);

        return $newStock;
    }

    public function getStock($productId)
    {
        $stock = PostMeta::where('post_id', $productId)
            ->where('meta_key', Constants::PRODUCT_META_STOCK)
            ->first();

        return $stock ? intval($stock->meta_value) : 0;
    }

    public function getLowStockProducts($threshold = 5)
    {
        return $this->model->product()
            ->whereHas('meta', function ($q) use ($threshold) {
            $q->where('meta_key', Constants::PRODUCT_META_STOCK)
                ->where('meta_value', '<=', $threshold)
                ->where('meta_value', '>', 0);
        })
            ->with(['meta'])
            ->get();
    }

    public function getOutOfStockProducts()
    {
        return $this->model->product()
            ->whereHas('meta', function ($q) {
            $q->where('meta_key', Constants::PRODUCT_META_STOCK_STATUS)
                ->where('meta_value', Constants::STOCK_STATUS_OUTOFSTOCK);
        })
            ->with(['meta'])
            ->get();
    }

    public function searchProducts($searchTerm, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        return $this->model->product()
            ->where(function ($query) use ($searchTerm) {
            $query->where(Post::COL_POST_TITLE, 'like', "%{$searchTerm}%")
                ->orWhere(Post::COL_POST_CONTENT, 'like', "%{$searchTerm}%")
                ->orWhere(Post::COL_POST_EXCERPT, 'like', "%{$searchTerm}%")
                ->orWhereHas('meta', function ($q) use ($searchTerm) {
                $q->where('meta_key', Constants::PRODUCT_META_SKU)
                    ->where('meta_value', 'like', "%{$searchTerm}%");
            }
            );
        })
            ->with(['meta', 'categories'])
            ->orderBy('post_date', 'desc')
            ->paginate($perPage);
    }

    public function getProductsByCategory($categorySlug, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        return $this->model->product()
            ->whereHas('categories', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        })
            ->with(['meta', 'categories'])
            ->orderBy('post_date', 'desc')
            ->paginate($perPage);
    }

    public function getProductsCount()
    {
        return $this->model->product()->count();
    }

    public function getVariationsCount()
    {
        return $this->model->productVariation()->count();
    }

    public function clearProductCache($productId = null)
    {
        if ($productId) {
            Cache::forget(Constants::CACHE_KEY_PRODUCT_PREFIX . $productId);
        }

        // Clear paginated shop caches
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_guest_page_' . $i);
            Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_' . Constants::USER_ROLE_CUSTOMER . '_page_' . $i);
            Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_' . Constants::USER_ROLE_SILVER . '_page_' . $i);
            Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_' . Constants::USER_ROLE_GOLD . '_page_' . $i);
        }
    }
}
