<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\OrderServiceInterface;
use App\DTOs\Order\OrderFilterDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\OrderFilterRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected readonly OrderServiceInterface $orderService
    ) {
    }

    #[OA\Post(
        path: '/api/orders',
        summary: 'Create an order from the authenticated user\'s cart',
        security: [['bearerAuth' => []]],
        tags: ['Order Module - User'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Order created successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/OrderResource'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request (e.g. empty cart)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Cart is empty'),
                    ]
                )
            ),
        ]
    )]
    public function store(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $order = $this->orderService->createOrderFromCart($userId);

            return $this->successResponse(
                new OrderResource($order),
                'Order created successfully',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    #[OA\Get(
        path: '/api/orders',
        summary: 'List the authenticated user\'s orders',
        security: [['bearerAuth' => []]],
        tags: ['Order Module - User'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(
                type: 'string',
                enum: ['pending', 'paid', 'cancelled']
            )),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(
                type: 'string',
                enum: ['asc', 'desc']
            )),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order list retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Order list retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/OrderResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta'),
                    ]
                )
            ),
        ]
    )]
    public function index(OrderFilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $dto = OrderFilterDTO::fromRequest($request->validated(), $userId);

        $orders = $this->orderService->getPaginatedOrders($dto);

        return $this->paginatedResponse(
            OrderResource::collection($orders),
            'Order list retrieved successfully'
        );
    }

    #[OA\Get(
        path: '/api/admin/orders',
        summary: 'List all orders (admin)',
        security: [['bearerAuth' => []]],
        tags: ['Order Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(
                type: 'string',
                enum: ['pending', 'paid', 'cancelled']
            )),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(
                type: 'string',
                enum: ['asc', 'desc']
            )),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order list retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Order list retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/OrderResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta'),
                    ]
                )
            ),
        ]
    )]
    public function adminIndex(OrderFilterRequest $request): JsonResponse
    {
        $dto = OrderFilterDTO::fromRequest($request->validated());

        $orders = $this->orderService->getPaginatedOrders($dto);

        return $this->paginatedResponse(
            OrderResource::collection($orders),
            'Order list retrieved successfully'
        );
    }

    #[OA\Get(
        path: '/api/orders/{order}',
        summary: 'Get a single order (user)',
        security: [['bearerAuth' => []]],
        tags: ['Order Module - User'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'success'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/OrderResource'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Order not found'),
        ]
    )]
    #[OA\Get(
        path: '/api/admin/orders/{order}',
        summary: 'Get a single order (admin)',
        security: [['bearerAuth' => []]],
        tags: ['Order Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'success'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/OrderResource'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Order not found'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $userId = null;
        if (!Auth::user()->isAdmin()) {
            $userId = Auth::id();
        }

        $order = $this->orderService->getOrderDetails($id, $userId);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        return $this->successResponse(new OrderResource($order));
    }

    #[OA\Patch(
        path: '/api/orders/{order}/status',
        summary: 'Update order status (user)',
        security: [['bearerAuth' => []]],
        tags: ['Order Module - User'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderStatusRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order status updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Order status updated successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid status transition',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid status transition'),
                    ]
                )
            ),
        ]
    )]
    #[OA\Patch(
        path: '/api/admin/orders/{order}/status',
        summary: 'Update order status (admin)',
        security: [['bearerAuth' => []]],
        tags: ['Order Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderStatusRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order status updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Order status updated successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid status transition',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid status transition'),
                    ]
                )
            ),
        ]
    )]
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateOrderStatusDTO::fromRequest($request);
            $this->orderService->updateOrderStatus($id, $dto);

            return $this->successResponse(null, 'Order status updated successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
