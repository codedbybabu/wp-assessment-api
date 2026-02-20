<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories($user = null)
    {
        $userRole = $user ? $user->role : null;

        $categories = $this->categoryRepository->getAllCategories($userRole);

        return $categories->map(function ($category) {
            return $this->formatCategoryResponse($category);
        });
    }

    public function getCategoryBySlug($slug)
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category) {
            return null;
        }

        return $this->formatCategoryResponse($category);
    }

    protected function formatCategoryResponse($category)
    {
        return [
            'id' => $category->term_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->taxonomy->description ?? null,
            'visibility' => $category->visibility,
            'product_count' => $category->taxonomy->count ?? 0
        ];
    }

    public function clearCategoryCache()
    {
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_guest');
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_' . Constants::USER_ROLE_CUSTOMER);
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_' . Constants::USER_ROLE_SILVER);
        Cache::forget(Constants::CACHE_CATEGORIES_KEY . '_' . Constants::USER_ROLE_GOLD);
    }
}
