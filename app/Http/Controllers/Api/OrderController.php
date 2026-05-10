<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\OrderServiceInterface;
use App\DTOs\Order\OrderFilterDTO;
use App\DTOs\Order\UpdateOrderStatusDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderFilterRequest;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected readonly OrderServiceInterface $orderService
    ) {}

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

    public function index(OrderFilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $dto = OrderFilterDTO::fromRequest($request->validated(), $userId);
        
        $orders = $this->orderService->getPaginatedOrders($dto);

        return $this->paginatedResponse(
            OrderResource::collection($orders)
            , 'Order list retrieved successfully'
        );
    }

    public function adminIndex(OrderFilterRequest $request): JsonResponse
    {
        $dto = OrderFilterDTO::fromRequest($request->validated());
        
        $orders = $this->orderService->getPaginatedOrders($dto);

        return $this->paginatedResponse(
            OrderResource::collection($orders)
            , 'Order list retrieved successfully'
        );
    }

    public function show(int $id): JsonResponse
    {
        $userId = null;
        if (! Auth::user()->isAdmin()) {
            $userId = Auth::id();
        }

        $order = $this->orderService->getOrderDetails($id, $userId);

        if (! $order) {
            return $this->errorResponse('Order not found', 404);
        }

        return $this->successResponse(new OrderResource($order));
    }

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
