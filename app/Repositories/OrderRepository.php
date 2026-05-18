<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\DTOs\Order\OrderFilterDTO;
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

        $now = now();
        $items = array_map(function (array $item) use ($order, $now): array {
            if (isset($item['options']) && is_array($item['options'])) {
                $item['options'] = json_encode($item['options']);
            }
            
            return array_merge($item, [
                'order_id'   => $order->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $orderItemsData);

        OrderItem::insert($items);

        return $order->load('user');
    }

    public function getOrdersByUserId(int $userId): LengthAwarePaginator|Collection
    {
        return Order::with('orderItems.product.images')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    public function getPaginatedOrders(OrderFilterDTO $dto): LengthAwarePaginator
    {
        $query = Order::with(['orderItems.product.images', 'user']);

        if ($dto->userId !== null) {
            $query->where('user_id', $dto->userId);
        }

        if ($dto->status !== null) {
            $query->where('status', $dto->status);
        }

        if ($dto->search !== null) {
            $search = $dto->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('orderItems.product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $query->orderBy($dto->sortBy, $dto->sortDir)
            ->paginate($dto->perPage);
    }

    public function findByIdAndUser(int $orderId, int $userId): ?Order
    {
        return Order::with('orderItems.product.images')
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public function findById(int $orderId): ?Order
    {
        return Order::with(['orderItems.product.images', 'user'])->find($orderId);
    }

    public function updateStatus(Order $order, OrderStatus $status): bool
    {
        return $order->update(['status' => $status->value]);
    }
}
