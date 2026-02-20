<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Post;
use App\Helpers\Constants;
use Illuminate\Support\Facades\Cache;

class OrderRepository
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function getUserOrders($userId, $limit = 10)
    {
        return $this->model->where('post_author', $userId)
            ->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->orderBy('post_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getUserOrdersPaginated($userId, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        return $this->model->where('post_author', $userId)
            ->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->orderBy('post_date', 'desc')
            ->paginate($perPage);
    }

    public function findById($orderId)
    {
        return $this->model->where('ID', $orderId)
            ->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->first();
    }

    public function getOrderWithDetails($orderId)
    {
        return $this->model->with(['meta', 'author'])
            ->where('ID', $orderId)
            ->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->first();
    }

    public function getOrdersByStatus($status, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        return $this->model->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->where('post_status', $status)
            ->orderBy('post_date', 'desc')
            ->paginate($perPage);
    }

    public function getRecentOrders($limit = 10)
    {
        return $this->model->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->orderBy('post_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOrderCountByUser($userId)
    {
        return $this->model->where('post_author', $userId)
            ->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->count();
    }

    public function getTotalSpentByUser($userId)
    {
        $orders = $this->model->where('post_author', $userId)
            ->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->where('post_status', Constants::ORDER_STATUS_COMPLETED)
            ->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += floatval($order->getMetaValue(Constants::ORDER_META_ORDER_TOTAL, 0));
        }

        return $total;
    }

    public function getOrderStats($userId = null)
    {
        $query = $this->model->where('post_type', Constants::POST_TYPE_SHOP_ORDER);

        if ($userId) {
            $query->where('post_author', $userId);
        }

        $orders = $query->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_completed' => 0,
            'total_processing' => 0,
            'total_pending' => 0,
            'total_cancelled' => 0,
            'total_spent' => 0
        ];

        foreach ($orders as $order) {
            $status = $order->post_status;
            $total = floatval($order->getMetaValue(Constants::ORDER_META_ORDER_TOTAL, 0));

            switch ($status) {
                case Constants::ORDER_STATUS_COMPLETED:
                    $stats['total_completed']++;
                    $stats['total_spent'] += $total;
                    break;
                case Constants::ORDER_STATUS_PROCESSING:
                    $stats['total_processing']++;
                    break;
                case Constants::ORDER_STATUS_PENDING:
                    $stats['total_pending']++;
                    break;
                case Constants::ORDER_STATUS_CANCELLED:
                    $stats['total_cancelled']++;
                    break;
            }
        }

        return $stats;
    }

    public function getMonthlyOrderStats($year = null, $userId = null)
    {
        $year = $year ?: date('Y');

        $query = $this->model->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->whereYear('post_date', $year);

        if ($userId) {
            $query->where('post_author', $userId);
        }

        $orders = $query->get();

        $stats = [];
        for ($month = 1; $month <= 12; $month++) {
            $stats[$month] = [
                'count' => 0,
                'total' => 0
            ];
        }

        foreach ($orders as $order) {
            $month = (int) date('n', strtotime($order->post_date));
            $stats[$month]['count']++;
            $stats[$month]['total'] += floatval($order->getMetaValue(Constants::ORDER_META_ORDER_TOTAL, 0));
        }

        return $stats;
    }

    public function searchOrders($searchTerm, $perPage = Constants::PAGINATE_PER_PAGE)
    {
        return $this->model->where('post_type', Constants::POST_TYPE_SHOP_ORDER)
            ->where(function ($query) use ($searchTerm) {
                $query->where('ID', 'like', "%{$searchTerm}%")
                    ->orWhereHas('meta', function ($q) use ($searchTerm) {
                        $q->where('meta_key', Constants::ORDER_META_BILLING_FIRST_NAME)
                          ->where('meta_value', 'like', "%{$searchTerm}%")
                          ->orWhere('meta_key', Constants::ORDER_META_BILLING_LAST_NAME)
                          ->where('meta_value', 'like', "%{$searchTerm}%")
                          ->orWhere('meta_key', Constants::ORDER_META_BILLING_EMAIL)
                          ->where('meta_value', 'like', "%{$searchTerm}%");
                    });
            })
            ->orderBy('post_date', 'desc')
            ->paginate($perPage);
    }

    public function clearOrderCache($orderId = null)
    {
        if ($orderId) {
            Cache::forget("order_{$orderId}");
            Cache::forget("order_details_{$orderId}");
        } else {
            // Clear all order caches - you might want to be more specific in production
            Cache::tags(['orders'])->flush();
        }
    }
}
