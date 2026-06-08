<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Services\CartServiceInterface;
use App\DTOs\Cart\AddToCartDTO;
use App\DTOs\Cart\UpdateCartItemDTO;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartService implements CartServiceInterface
{
    public function __construct(
        protected CartRepositoryInterface $cartRepository
    ) {}

    public function getCart(int $userId): ?Cart
    {
        return $this->cartRepository->getCartByUserId($userId);
    }

    public function addToCart(int $userId, AddToCartDTO $dto): CartItem
    {
        // Lazy initialization
        $cart = $this->cartRepository->getCartByUserId($userId);
        if (! $cart) {
            $cart = $this->cartRepository->createCart($userId);
        }

        $variantId = $this->resolveVariantId($dto);

        $existingItem = $this->cartRepository->getCartItem($cart->id, $dto->product_id, $variantId);

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $dto->quantity;
            $this->cartRepository->updateCartItem($existingItem->id, $newQuantity);
            $existingItem->quantity = $newQuantity;

            return $existingItem;
        }

        return $this->cartRepository->addCartItem($cart->id, $dto->product_id, $dto->quantity, $variantId);
    }

    public function updateCartItem(int $userId, int $itemId, UpdateCartItemDTO $dto): bool
    {
        $belongsToUser = $this->cartRepository->verifyItemBelongsToUser($itemId, $userId);

        if (! $belongsToUser) {
            throw new NotFoundHttpException('Cart item not found or access denied.');
        }

        return $this->cartRepository->updateCartItem($itemId, $dto->quantity);
    }

    public function removeCartItem(int $userId, int $itemId): bool
    {
        $belongsToUser = $this->cartRepository->verifyItemBelongsToUser($itemId, $userId);

        if (! $belongsToUser) {
            throw new NotFoundHttpException('Cart item not found or access denied.');
        }

        $isDeleted = $this->cartRepository->deleteCartItem($itemId);
        $count = $this->cartRepository->countCartItemsByUserId($userId);
        if ($count == 0) {
            $this->cartRepository->deleteCartByUserId($userId);
        }

        return $isDeleted;
    }

    public function countCartItemsByUser(int $userId): int
    {
        return $this->cartRepository->countCartItemsByUserId($userId);
    }

    public function clearCart(int $userId): bool
    {
        return $this->cartRepository->deleteCartByUserId($userId);
    }

    /**
     * Resolve product_variant_id from the DTO:
     *   - Mode 1: product_variant_id is sent directly → use as-is.
     *   - Mode 2: color_id + size_id are sent → lookup ProductVariant by product_id.
     */
    private function resolveVariantId(AddToCartDTO $dto): ?int
    {
        if ($dto->product_variant_id !== null) {
            return $dto->product_variant_id;
        }

        if ($dto->color_id !== null || $dto->size_id !== null) {
            $variant = ProductVariant::where('product_id', $dto->product_id)
                ->where('color_id', $dto->color_id)
                ->where('size_id', $dto->size_id)
                ->first();

            return $variant?->id;
        }

        return null;
    }
}
