<?php

namespace App\Contracts\Repositories;

use App\Models\Cart;
use App\Models\CartItem;

interface CartRepositoryInterface
{
    public function getCartByUserId(int $userId): ?Cart;

    public function createCart(int $userId): Cart;

    public function deleteCartByUserId(int $userId): bool;

    public function countCartItemsByUserId(int $userId): int;

    public function getCartItem(int $cartId, int $productId, ?array $options = null): ?CartItem;

    public function addCartItem(int $cartId, int $productId, int $quantity, ?array $options = null): CartItem;

    public function updateCartItem(int $itemId, int $quantity): bool;

    public function deleteCartItem(int $itemId): bool;

    public function verifyItemBelongsToUser(int $itemId, int $userId): bool;
}
