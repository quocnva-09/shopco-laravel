<?php

namespace App\Contracts\Services;

use App\DTOs\AddToCartDTO;
use App\DTOs\UpdateCartItemDTO;
use App\Models\Cart;
use App\Models\CartItem;

interface CartServiceInterface
{
    public function getCart(int $userId): ?Cart;

    public function addToCart(int $userId, AddToCartDTO $dto): CartItem;

    public function updateCartItem(int $userId, int $itemId, UpdateCartItemDTO $dto): bool;

    public function removeCartItem(int $userId, int $itemId): bool;

    public function clearCart(int $userId): bool;

    public function countCartItemsByUser(int $userId): int;
}
