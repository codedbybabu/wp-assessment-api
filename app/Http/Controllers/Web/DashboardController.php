<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $authService;
    protected $orderRepository;

    public function __construct(AuthService $authService, OrderRepository $orderRepository)
    {
        $this->authService = $authService;
        $this->orderRepository = $orderRepository;
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $recentOrders = $this->orderRepository->getUserOrders($user->ID, 5);

        return view('dashboard.index', [
            'user' => $user,
            'recentOrders' => $recentOrders
        ]);
    }

    public function customer()
    {
        return view('dashboard.customer');
    }

    public function silver()
    {
        return view('dashboard.silver');
    }

    public function gold()
    {
        return view('dashboard.gold');
    }
}
