<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Order\OrderFilterDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function createOrderFromCart(int $userId): Order;

    public function getPaginatedOrders(OrderFilterDTO $dto): LengthAwarePaginator;

    public function getOrderDetails(int $orderId, ?int $userId = null): ?Order;

    public function updateOrderStatus(int $orderId, UpdateOrderStatusDTO $dto): bool;
}
