<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function createOrderFromCart(int $userId): Order;

    public function getUserOrders(int $userId): LengthAwarePaginator|Collection;

    public function getOrderDetails(int $userId, int $orderId): ?Order;

    public function updateOrderStatus(int $orderId, UpdateOrderStatusDTO $dto): bool;
}
