<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductRequest;
use App\Services\ProductService;
use App\Helpers\Constants;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function show(ProductRequest $request, $identifier)
    {
        try {
            $user = auth('api')->user();

            // Check if identifier is ID or slug
            $product = is_numeric($identifier)
                ? $this->productService->getProductById($identifier, $user)
                : $this->productService->getProductBySlug($identifier, $user);

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found'
                ], Constants::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'data' => $product
            ], Constants::HTTP_OK);

        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load product: ' . $e->getMessage()
            ], Constants::HTTP_SERVER_ERROR);
        }
    }

    public function variations(Request $request, $productId)
    {
        try {
            $user = auth('api')->user();
            $product = $this->productService->getProductById($productId, $user, true);

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found'
                ], Constants::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'variations' => $product['variations'] ?? []
                ]
            ], Constants::HTTP_OK);

        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load variations'
            ], Constants::HTTP_SERVER_ERROR);
        }
    }
}
