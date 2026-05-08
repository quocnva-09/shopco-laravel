<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\CartServiceInterface;
use App\DTOs\AddToCartDTO;
use App\DTOs\UpdateCartItemDTO;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected CartServiceInterface $cartService
    ) {
    }

    public function index(): JsonResponse
    {
        $cart = $this->cartService->getCart(Auth::id());

        if (!$cart) {
            return $this->successResponse(null, 'Cart is empty');
        }

        return $this->successResponse(new CartResource($cart), 'Cart retrieved successfully');
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        $dto = AddToCartDTO::fromRequest($request);
        $cartItem = $this->cartService->addToCart(Auth::id(), $dto);

        return $this->successResponse(new CartItemResource($cartItem), 'Item added to cart successfully');
    }

    public function updateItem(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        $dto = UpdateCartItemDTO::fromRequest($request);
        $isUpdated = $this->cartService->updateCartItem(Auth::id(), $itemId, $dto);

        return $this->successResponse($isUpdated, 'Cart item updated successfully');
    }

    public function removeItem(int $itemId): JsonResponse
    {
        $isRemoved = $this->cartService->removeCartItem(Auth::id(), $itemId);

        return $this->successResponse($isRemoved, 'Cart item removed successfully');
    }

    public function clear(): JsonResponse
    {
        $isCleared = $this->cartService->clearCart(Auth::id());

        return $this->successResponse($isCleared, 'Cart cleared successfully');
    }

    public function countCartItems(): JsonResponse
    {
        $count = $this->cartService->countCartItemsByUser(Auth::id());

        return $this->successResponse($count, 'Cart items count retrieved successfully');
    }
}
