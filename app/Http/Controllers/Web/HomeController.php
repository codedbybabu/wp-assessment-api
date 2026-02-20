<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Get featured products (limit 4)
        $featuredProducts = $this->productService->getShopProducts($user, 4);

        // Get categories for the navigation
        $categories = $this->categoryService->getAllCategories($user);

        return view('home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories
        ]);
    }

    public function shop()
    {
        return view('shop.index');
    }

    public function product($slug)
    {
        return view('shop.show', ['slug' => $slug]);
    }

    public function login()
    {
        return view('auth.login');
    }
}
