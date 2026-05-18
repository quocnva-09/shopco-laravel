<?php

namespace App\Repositories;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;


class CartRepository implements CartRepositoryInterface
{
    public function getCartByUserId(int $userId): ?Cart
    {
        return Cart::with('cartItems.product')->where('user_id', $userId)->first();
    }

    public function createCart(int $userId): Cart
    {
        return Cart::create(['user_id' => $userId]);
    }

    public function getCartItem(int $cartId, int $productId, ?array $options = null): ?CartItem
    {
        $query = CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId);

        if (empty($options)) {
            $query->whereNull('options');
        } else {
            // Sort options to ensure consistent JSON encoding
            ksort($options);
            $query->where('options', json_encode($options));
        }

        return $query->first();
    }

    public function addCartItem(int $cartId, int $productId, int $quantity, ?array $options = null): CartItem
    {
        if (is_array($options)) {
            ksort($options);
        }

        return CartItem::create([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'options' => $options,
        ]);
    }

    public function updateCartItem(int $itemId, int $quantity): bool
    {
        return (bool) CartItem::where('id', $itemId)->update(['quantity' => $quantity]);
    }

    public function deleteCartItem(int $itemId): bool
    {
        return (bool) CartItem::where('id', $itemId)->delete();
    }

    public function deleteCartByUserId(int $userId): bool
    {
        return (bool) Cart::where('user_id', $userId)->delete();
    }

    public function verifyItemBelongsToUser(int $itemId, int $userId): bool
    {
        return CartItem::join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('cart_items.id', $itemId)
            ->where('carts.user_id', $userId)
            ->exists();
    }

    public function countCartItemsByUserId(int $userId): int
    {
        return CartItem::join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('carts.user_id', $userId)
            ->sum('cart_items.quantity');
    }
}
