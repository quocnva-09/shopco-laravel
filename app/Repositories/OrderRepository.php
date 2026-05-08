<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function createOrder(int $userId, array $orderData, array $orderItemsData): Order
    {
        $order = Order::create(array_merge(['user_id' => $userId], $orderData));

        foreach ($orderItemsData as $itemData) {
            $order->orderItems()->create($itemData);
        }

        return $order;
    }

    public function getOrdersByUserId(int $userId): LengthAwarePaginator|Collection
    {
        return Order::with('orderItems.product')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    public function findByIdAndUser(int $orderId, int $userId): ?Order
    {
        return Order::with('orderItems.product')
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public function updateStatus(Order $order, OrderStatus $status): bool
    {
        return $order->update(['status' => $status->value]);
    }
}
