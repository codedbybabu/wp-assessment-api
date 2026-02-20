<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Helpers\Constants;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    protected $productRepository;
    protected $pricingService;

    public function __construct(
        ProductRepository $productRepository,
        PricingService $pricingService
    ) {
        $this->productRepository = $productRepository;
        $this->pricingService = $pricingService;
    }

    // public function getShopProducts($user = null)
    // {
    //     $userRole = $user ? $user->role : null;

    //     $products = $this->productRepository->getShopProducts($userRole);

    //     // Transform products with role-based pricing
    //     $products->getCollection()->transform(function ($product) use ($userRole) {
    //         return $this->formatProductResponse($product, $userRole);
    //     });

    //     return $products;
    // }

     public function getShopProducts($user = null, $perPage = Constants::PAGINATE_PER_PAGE, $search = null, $category = null)
    {
        $userRole = $user ? $user->role : null;

        $products = $this->productRepository->getShopProducts($userRole, $perPage, $search, $category);

        // Transform products with role-based pricing
        $products->getCollection()->transform(function ($product) use ($userRole) {
            return $this->formatProductResponse($product, $userRole);
        });

        return $products;
    }

    public function getProductBySlug($slug, $user = null)
    {
        $userRole = $user ? $user->role : null;

        $product = $this->productRepository->findBySlug($slug);

        if (!$product) {
            return null;
        }

        return $this->formatProductResponse($product, $userRole, true);
    }

    public function getProductById($id, $user = null)
    {
        $userRole = $user ? $user->role : null;

        $product = $this->productRepository->findById($id);

        if (!$product) {
            return null;
        }

        return $this->formatProductResponse($product, $userRole, true);
    }

    protected function formatProductResponse($product, $userRole = null, $includeVariations = false)
    {
        $response = [
            'id' => $product->ID,
            'name' => $product->post_title,
            'slug' => $product->post_name,
            'description' => $product->post_content,
            'excerpt' => $product->post_excerpt,
            'sku' => $product->sku,
            'stock' => $product->stock,
            'stock_status' => $product->stock_status,
            'price' => $this->pricingService->getPriceForRole($product, $userRole),
            'categories' => $this->formatCategories($product->categories),
            'created_at' => $product->post_date,
            'updated_at' => $product->post_modified
        ];

        if ($includeVariations) {
            $response['variations'] = $this->getVariations($product->children, $userRole);
            $response['is_variable'] = count($response['variations']) > 0;
        }

        return $response;
    }

    protected function formatCategories($categories)
    {
        return $categories->map(function ($category) {
            return [
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'visibility' => $category->visibility
            ];
        });
    }

    protected function getVariations($variations, $userRole = null)
    {
        return $variations->map(function ($variation) use ($userRole) {
            return [
                'id' => $variation->ID,
                'sku' => $variation->sku,
                'attributes' => $this->parseVariationAttributes($variation->post_excerpt),
                'stock' => $variation->stock,
                'stock_status' => $variation->stock_status,
                'price' => $this->pricingService->getPriceForRole($variation, $userRole)
            ];
        });
    }

    protected function parseVariationAttributes($excerpt)
    {
        $attributes = [];

        if (preg_match_all('/([^:]+):\s*([^\n]+)/', $excerpt, $matches)) {
            foreach ($matches[1] as $index => $key) {
                $attributes[trim($key)] = trim($matches[2][$index] ?? '');
            }
        }

        return $attributes;
    }

    public function clearProductCache()
    {
        Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_guest');
        Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_' . Constants::USER_ROLE_CUSTOMER);
        Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_' . Constants::USER_ROLE_SILVER);
        Cache::forget(Constants::CACHE_PRODUCTS_KEY . '_shop_' . Constants::USER_ROLE_GOLD);
    }


}
