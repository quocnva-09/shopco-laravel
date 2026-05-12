<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\CartServiceInterface;
use App\DTOs\Cart\AddToCartDTO;
use App\DTOs\Cart\UpdateCartItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected CartServiceInterface $cartService
    ) {}

    #[OA\Get(
        path: '/api/cart',
        summary: 'Get the authenticated user\'s cart',
        security: [['bearerAuth' => []]],
        tags: ['User - Cart Module'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Cart retrieved successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CartResource'),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $cart = $this->cartService->getCart(Auth::id());

        if (! $cart) {
            return $this->successResponse(null, 'Cart is empty');
        }

        return $this->successResponse(new CartResource($cart), 'Cart retrieved successfully');
    }

    #[OA\Post(
        path: '/api/cart/add',
        summary: 'Add a product to the cart',
        security: [['bearerAuth' => []]],
        tags: ['User - Cart Module'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AddToCartRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Item added to cart successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Item added to cart successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CartItemResource'),
                    ]
                )
            ),
        ]
    )]
    public function add(AddToCartRequest $request): JsonResponse
    {
        $dto = AddToCartDTO::fromRequest($request);
        $cartItem = $this->cartService->addToCart(Auth::id(), $dto);

        return $this->successResponse(new CartItemResource($cartItem), 'Item added to cart successfully');
    }

    #[OA\Put(
        path: '/api/cart/items/{itemId}',
        summary: 'Update the quantity of a cart item',
        security: [['bearerAuth' => []]],
        tags: ['User - Cart Module'],
        parameters: [
            new OA\Parameter(name: 'itemId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateCartItemRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart item updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Cart item updated successfully'),
                        new OA\Property(property: 'data', type: 'boolean', example: true),
                    ]
                )
            ),
        ]
    )]
    public function updateItem(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        $dto = UpdateCartItemDTO::fromRequest($request);
        $isUpdated = $this->cartService->updateCartItem(Auth::id(), $itemId, $dto);

        return $this->successResponse($isUpdated, 'Cart item updated successfully');
    }

    #[OA\Delete(
        path: '/api/cart/items/{itemId}',
        summary: 'Remove a specific item from the cart',
        security: [['bearerAuth' => []]],
        tags: ['User - Cart Module'],
        parameters: [
            new OA\Parameter(name: 'itemId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart item removed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Cart item removed successfully'),
                        new OA\Property(property: 'data', type: 'boolean', example: true),
                    ]
                )
            ),
        ]
    )]
    public function removeItem(int $itemId): JsonResponse
    {
        $isRemoved = $this->cartService->removeCartItem(Auth::id(), $itemId);

        return $this->successResponse($isRemoved, 'Cart item removed successfully');
    }

    #[OA\Get(
        path: '/api/cart/items/count',
        summary: 'Get the total number of items in the cart',
        security: [['bearerAuth' => []]],
        tags: ['User - Cart Module'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart items count retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Cart items count retrieved successfully'
                        ),
                        new OA\Property(property: 'data', type: 'integer', example: 5),
                    ]
                )
            ),
        ]
    )]
    public function countCartItems(): JsonResponse
    {
        $count = $this->cartService->countCartItemsByUser(Auth::id());

        return $this->successResponse($count, 'Cart items count retrieved successfully');
    }

    #[OA\Delete(
        path: '/api/cart',
        summary: 'Clear all items from the cart',
        security: [['bearerAuth' => []]],
        tags: ['User - Cart Module'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart cleared successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Cart cleared successfully'),
                        new OA\Property(property: 'data', type: 'boolean', example: true),
                    ]
                )
            ),
        ]
    )]
    public function clear(): JsonResponse
    {
        $isCleared = $this->cartService->clearCart(Auth::id());

        return $this->successResponse($isCleared, 'Cart cleared successfully');
    }
}
