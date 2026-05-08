<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function createOrder(int $userId, array $orderData, array $orderItemsData): Order;

    public function getOrdersByUserId(int $userId): LengthAwarePaginator|Collection;

    public function findByIdAndUser(int $orderId, int $userId): ?Order;

    public function updateStatus(Order $order, OrderStatus $status): bool;
}
