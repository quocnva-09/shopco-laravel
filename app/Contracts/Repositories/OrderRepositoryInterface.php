<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTOs\Order\OrderFilterDTO;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function createOrder(int $userId, array $orderData, array $orderItemsData): Order;

    public function getOrdersByUserId(int $userId): LengthAwarePaginator|Collection;

    public function getPaginatedOrders(OrderFilterDTO $dto): LengthAwarePaginator;

    public function findByIdAndUser(int $orderId, int $userId): ?Order;

    public function findById(int $orderId): ?Order;

    public function updateStatus(Order $order, OrderStatus $status): bool;
}
