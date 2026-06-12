<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\GuestOrderServiceInterface;
use App\DTOs\Order\GuestCheckoutDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\GuestCheckoutRequest;
use App\Http\Requests\Order\VerifyGuestOrderOtpRequest;
use App\Http\Resources\OrderResource;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class GuestOrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected readonly GuestOrderServiceInterface $guestOrderService
    ) {}

    #[OA\Post(
        path: '/api/guest/orders/checkout',
        summary: 'Guest checkout — create an order without authentication',
        tags: ['Guest Module'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GuestCheckoutRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Order placed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_CREATED),
                        new OA\Property(property: 'message', type: 'string', example: 'Order placed successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/OrderResource'),
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Invalid product in cart',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_UNPROCESSABLE_ENTITY),
                        new OA\Property(property: 'message', type: 'string', example: 'Product ID 99 does not exist.'),
                    ]
                )
            ),
        ]
    )]
    public function checkout(GuestCheckoutRequest $request): JsonResponse
    {
        try {
            $order = $this->guestOrderService->checkout(
                GuestCheckoutDTO::fromRequest($request)
            );

            return $this->successResponse(
                new OrderResource($order),
                __('response.order.guest_checkout'),
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[OA\Post(
        path: '/api/guest/orders/{order_id}/verify-otp',
        summary: 'Verify guest order OTP',
        tags: ['Guest Module'],
        parameters: [
            new OA\Parameter(
                name: 'order_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/VerifyGuestOrderOtpRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OTP verified successfully'
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Invalid or expired OTP'
            )
        ]
    )]
    public function verifyOtp(int $orderId, VerifyGuestOrderOtpRequest $request): JsonResponse
    {
        try {
            $this->guestOrderService->verifyOtp($orderId, $request->input('otp'));

            return $this->successResponse(
                null,
                'OTP verified successfully.',
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Post(
        path: '/api/guest/orders/{order_id}/resend-otp',
        summary: 'Resend guest order OTP',
        tags: ['Guest Module'],
        parameters: [
            new OA\Parameter(
                name: 'order_id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OTP resent successfully'
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Order already verified or error occurred'
            )
        ]
    )]
    public function resendOtp(int $orderId): JsonResponse
    {
        try {
            $this->guestOrderService->resendOtp($orderId);

            return $this->successResponse(
                null,
                'OTP resent successfully.',
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
