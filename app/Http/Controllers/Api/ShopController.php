<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Helpers\Constants;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService
        )
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            $products = $this->productService->getShopProducts($user);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'products' => $products->items(),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'next_page_url' => $products->nextPageUrl(),
                        'prev_page_url' => $products->previousPageUrl()
                    ]
                ]
            ], Constants::HTTP_OK);

        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load products: ' . $e->getMessage()
            ], Constants::HTTP_SERVER_ERROR);
        }
    }

    public function categories(Request $request)
    {
        try {
            $user = auth('api')->user();
            $categories = $this->categoryService->getAllCategories($user);

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], Constants::HTTP_OK);

        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load categories'
            ], Constants::HTTP_SERVER_ERROR);
        }
    }
}
