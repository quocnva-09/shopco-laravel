<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ReviewServiceInterface;
use App\DTOs\Review\GuestReviewDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\GuestReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class GuestReviewController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected readonly ReviewServiceInterface $reviewService
    ) {}

    #[OA\Post(
        path: '/api/guest/reviews',
        summary: 'Submit a guest review without authentication',
        tags: ['Guest Module'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/GuestReviewRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Review submitted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_CREATED),
                        new OA\Property(property: 'message', type: 'string', example: 'Review submitted successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ReviewResource'),
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Order not found'
            ),
            new OA\Response(
                response: Response::HTTP_FORBIDDEN,
                description: 'Order is not paid or review not eligible'
            ),
            new OA\Response(
                response: Response::HTTP_CONFLICT,
                description: 'Order has already been reviewed'
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Product does not belong to this order'
            ),
        ]
    )]
    public function store(GuestReviewRequest $request): JsonResponse
    {
        try {
            $review = $this->reviewService->createGuestReview(
                GuestReviewDTO::fromRequest($request)
            );

            return $this->successResponse(
                new ReviewResource($review),
                __('response.review.guest_created'),
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
    }
}
