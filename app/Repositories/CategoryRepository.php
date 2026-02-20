<?php

namespace App\Repositories;

use App\Models\Term;
use App\Models\TermMeta;
use App\Models\TermTaxonomy;
use App\Helpers\Constants;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryRepository
{
    protected $model;

    public function __construct(Term $model)
    {
        $this->model = $model;
    }

    public function getAllCategories($userRole = null)
    {
        $cacheKey = Constants::CACHE_CATEGORIES_KEY . '_' . ($userRole ?? 'guest');

        return Cache::remember($cacheKey, Constants::CACHE_TTL_ONE_DAY, function () use ($userRole) {
            $query = $this->model->whereHas('taxonomy', function ($q) {
                    $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT);
                }
                );

                if (!$userRole) {
                    // Guest: only public categories
                    $query->whereHas('meta', function ($q) {
                            $q->where('meta_key', Constants::TERM_META_VISIBILITY)
                                ->where('meta_value', Constants::CATEGORY_VISIBILITY_PUBLIC);
                        }
                        );
                    }

                    return $query->with(['meta', 'taxonomy'])->get();
                });
    }

    public function findById($id)
    {
        return $this->model->whereHas('taxonomy', function ($q) {
            $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT);
        })->with(['meta', 'taxonomy'])->find($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->where(Term::COL_SLUG, $slug)
            ->whereHas('taxonomy', function ($q) {
            $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT);
        })
            ->with(['meta', 'taxonomy'])
            ->first();
    }

    public function getCategoryVisibility($categoryId)
    {
        $visibility = TermMeta::where('term_id', $categoryId)
            ->where('meta_key', Constants::TERM_META_VISIBILITY)
            ->first();

        return $visibility ? $visibility->meta_value : Constants::CATEGORY_VISIBILITY_PUBLIC;
    }

    public function updateVisibility($categoryId, $visibility)
    {
        return TermMeta::updateOrCreate(
        [
            'term_id' => $categoryId,
            'meta_key' => Constants::TERM_META_VISIBILITY
        ],
        [
            'meta_value' => $visibility
        ]
        );
    }

    public function getCategoryTree($parentId = 0)
    {
        $categories = $this->model->whereHas('taxonomy', function ($q) use ($parentId) {
            $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT)
                ->where('parent', $parentId);
        })->with(['meta', 'taxonomy'])->get();

        foreach ($categories as $category) {
            $category->children = $this->getCategoryTree($category->taxonomy->term_taxonomy_id);
        }

        return $categories;
    }

    public function getCategoriesWithProductCount()
    {
        return $this->model->whereHas('taxonomy', function ($q) {
            $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT);
        })
            ->with(['meta', 'taxonomy'])
            ->get()
            ->map(function ($category) {
            return [
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'visibility' => $category->visibility,
                'product_count' => $category->taxonomy->count ?? 0,
                'description' => $category->taxonomy->description ?? ''
            ];
        });
    }

    public function getCategoryProducts($categoryId, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        $category = $this->findById($categoryId);

        if (!$category) {
            return null;
        }

        return $category->products()
            ->where('post_type', Constants::POST_TYPE_PRODUCT)
            ->where('post_status', Constants::POST_STATUS_PUBLISH)
            ->with(['meta'])
            ->orderBy('post_date', 'desc')
            ->paginate($perPage);
    }

    public function createCategory($data)
    {
        // Create term
        $term = $this->model->create([
            Term::COL_NAME => $data['name'],
            Term::COL_SLUG => $data['slug'] ?? Str::slug($data['name']),
            Term::COL_TERM_GROUP => 0
        ]);

        // Create taxonomy
        TermTaxonomy::create([
            'term_id' => $term->term_id,
            'taxonomy' => Constants::TAXONOMY_PRODUCT_CAT,
            'description' => $data['description'] ?? '',
            'parent' => $data['parent'] ?? 0,
            'count' => 0
        ]);

        // Set visibility
        if (isset($data['visibility'])) {
            $this->updateVisibility($term->term_id, $data['visibility']);
        }

        $this->clearCategoryCache();

        return $term;
    }

    public function updateCategory($id, $data)
    {
        $term = $this->findById($id);

        if (!$term) {
            return null;
        }

        // Update term
        $term->update([
            Term::COL_NAME => $data['name'] ?? $term->name,
            Term::COL_SLUG => $data['slug'] ?? $term->slug
        ]);

        // Update taxonomy
        if ($term->taxonomy) {
            $term->taxonomy->update([
                'description' => $data['description'] ?? $term->taxonomy->description,
                'parent' => $data['parent'] ?? $term->taxonomy->parent
            ]);
        }

        // Update visibility
        if (isset($data['visibility'])) {
            $this->updateVisibility($term->term_id, $data['visibility']);
        }

        $this->clearCategoryCache();

        return $term->fresh(['meta', 'taxonomy']);
    }

    public function deleteCategory($id)
    {
        $term = $this->findById($id);

        if (!$term) {
            return false;
        }

        // Delete meta
        TermMeta::where('term_id', $id)->delete();

        // Delete taxonomy
        if ($term->taxonomy) {
            $term->taxonomy->delete();
        }

        // Delete term
        $result = $term->delete();

        $this->clearCategoryCache();

        return $result;
    }

    public function getCategoryStats()
    {
        $total = $this->model->whereHas('taxonomy', function ($q) {
            $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT);
        })->count();

        $public = $this->model->whereHas('meta', function ($q) {
            $q->where('meta_key', Constants::TERM_META_VISIBILITY)
                ->where('meta_value', Constants::CATEGORY_VISIBILITY_PUBLIC);
        })->count();

        $protected = $this->model->whereHas('meta', function ($q) {
            $q->where('meta_key', Constants::TERM_META_VISIBILITY)
                ->where('meta_value', Constants::CATEGORY_VISIBILITY_PROTECTED);
        })->count();

        $withProducts = $this->model->whereHas('taxonomy', function ($q) {
            $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT)
                ->where('count', '>', 0);
        })->count();

        return [
            'total' => $total,
            'public' => $public,
            'protected' => $protected,
            'with_products' => $withProducts,
            'empty' => $total - $withProducts
        ];
    }

    public function searchCategories($searchTerm)
    {
        return $this->model->whereHas('taxonomy', function ($q) {
            $q->where('taxonomy', Constants::TAXONOMY_PRODUCT_CAT);
        })
            ->where(function ($query) use ($searchTerm) {
            $query->where(Term::COL_NAME, 'like', "%{$searchTerm}%")
                ->orWhere(Term::COL_SLUG, 'like', "%{$searchTerm}%")
                ->orWhereHas('taxonomy', function ($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%");
            }
            );
        })
            ->with(['meta', 'taxonomy'])
            ->get();
    }

    public function clearCategoryCache()
    {
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_guest');
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_' . Constants::USER_ROLE_CUSTOMER);
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_' . Constants::USER_ROLE_SILVER);
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_' . Constants::USER_ROLE_GOLD);
    }
}
