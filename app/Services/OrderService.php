<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\DTOs\Order\OrderFilterDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Mail\OrderCreated;
use App\Models\Order;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
        protected readonly CartServiceInterface $cartService,
        protected readonly UserServiceInterface $userService,
    ) {
    }

    public function createOrderFromCart(int $userId): Order
    {
        $cart = $this->cartService->getCart($userId);

        if (!$cart || $cart->cartItems->isEmpty()) {
            throw new Exception('Cart is empty. Cannot create order.');
        }

        $order = DB::transaction(function () use ($userId, $cart) {
            $orderItemsData = [];
            $totalAmount = 0.0;

            // Load product (id, name, price) và productVariant với color/size để snapshot
            $cart->load([
                'cartItems.product:id,name,price',
                'cartItems.productVariant.color',
                'cartItems.productVariant.size',
            ]);

            foreach ($cart->cartItems as $cartItem) {
                $price      = (float) $cartItem->product->price;
                $quantity   = $cartItem->quantity;
                $totalMoney = $price * $quantity;

                $orderItemsData[] = [
                    'product_id'           => $cartItem->product_id,
                    'product_variant_id'   => $cartItem->product_variant_id,
                    'product_name'         => $cartItem->product->name,
                    'product_variant_name' => $cartItem->productVariant?->variant_name ?? null,
                    'quantity'             => $quantity,
                    'price'                => $price,
                    'totalMoney'           => $totalMoney,
                ];

                $totalAmount += $totalMoney;
            }

            $orderData = [
                'totalAmount' => $totalAmount,
            ];

            $order = $this->orderRepository->createOrder($userId, $orderData, $orderItemsData);

            $this->cartService->clearCart($userId);

            return $order;
        });

        Mail::to($order->user->email)->later(now()->addSeconds(30), new OrderCreated($order));

        return $order;
    }

    public function getPaginatedOrders(OrderFilterDTO $dto): LengthAwarePaginator
    {
        return $this->orderRepository->getPaginatedOrders($dto);
    }

    public function getOrderDetails(int $orderId, ?int $userId = null): ?Order
    {
        if ($userId) {
            return $this->orderRepository->findByIdAndUser($orderId, $userId);
        }

        return $this->orderRepository->findById($orderId);
    }

    public function updateOrderStatus(int $orderId, UpdateOrderStatusDTO $dto): bool
    {
        $order = Order::findOrFail($orderId);

        return $this->orderRepository->updateStatus($order, $dto->status);
    }
}
