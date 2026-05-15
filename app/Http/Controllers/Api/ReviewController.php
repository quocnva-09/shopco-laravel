<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Contracts\Services\ReviewServiceInterface;
use App\DTOs\Review\ReviewApproveDTO;
use App\DTOs\Review\ReviewDTO;
use App\DTOs\Review\ReviewFilterDTO;
use App\Http\Requests\Review\ReviewApproveRequest;
use App\Http\Requests\Review\ReviewFilterRequest;
use App\Http\Requests\Review\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Traits\ApiResponseTrait;
use Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ReviewServiceInterface $reviewService
    ) {
    }

    #[OA\Get(
        path: "/api/admin/reviews",
        operationId: "getAdminReviews",
        summary: "Get a list of all reviews (Admin only)",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Review Module"],
        parameters: [
            new OA\Parameter(name: "keyword", in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "sort_by", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "created_at", "rating"])),
            new OA\Parameter(name: "sort_direction", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"])),
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of reviews",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "integer", example: 200),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/ReviewResource")
                        ),
                        new OA\Property(property: "meta", type: "object"),
                        new OA\Property(property: "links", type: "object")
                    ]
                )
            )
        ]
    )]
    public function index(ReviewFilterRequest $request): JsonResponse
    {
        $reviews = $this->reviewService->getList(ReviewFilterDTO::fromRequest($request));

        return $this->paginatedResponse(ReviewResource::collection($reviews));
    }

    #[OA\Get(
        path: "/api/admin/reviews/{id}",
        operationId: "getAdminReviewById",
        summary: "Get a review by ID",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Review Module"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Review details",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "integer", example: 200),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(property: "data", ref: "#/components/schemas/ReviewResource")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Review not found")
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review) {
            return $this->errorResponse('Review not found', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse(new ReviewResource($review));
    }

    #[OA\Post(
        path: "/api/reviews",
        operationId: "createUserReview",
        summary: "Create a new review for a product",
        security: [["bearerAuth" => []]],
        tags: ["User - Review Module"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ReviewRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Review created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "integer", example: 201),
                        new OA\Property(property: "message", type: "string", example: "Review created successfully."),
                        new OA\Property(property: "data", ref: "#/components/schemas/ReviewResource")
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Not eligible to review this product")
        ]
    )]
    public function store(ReviewRequest $request): JsonResponse
    {
        $review = $this->reviewService->create(ReviewDTO::fromRequest($request));

        return $this->successResponse(
            new ReviewResource($review),
            'Review created successfully.',
            Response::HTTP_CREATED
        );
    }

    #[OA\Patch(
        path: "/api/admin/reviews/{id}/approve",
        operationId: "approveReview",
        summary: "Approve or reject a review",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Review Module"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ReviewApproveRequest")
        ),
        responses: [
            new OA\Response(response: 200, description: "Review status updated successfully"),
            new OA\Response(response: 404, description: "Review not found")
        ]
    )]
    public function approve(int $id, ReviewApproveRequest $request): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review) {
            return $this->errorResponse('Review not found', Response::HTTP_NOT_FOUND);
        }

        $this->reviewService->approve($review, ReviewApproveDTO::fromRequest($request));

        return $this->successResponse([], 'Review status updated successfully.');
    }

    #[OA\Delete(
        path: "/api/admin/reviews/{id}",
        operationId: "deleteAdminReview",
        summary: "Delete a review",
        security: [["bearerAuth" => []]],
        tags: ["Admin - Review Module"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Review deleted successfully"),
            new OA\Response(response: 404, description: "Review not found")
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review) {
            return $this->errorResponse('Review not found', Response::HTTP_NOT_FOUND);
        }

        $this->reviewService->delete($review);

        return $this->successResponse([], 'Review deleted successfully.');
    }

    #[OA\Get(
        path: "/api/products/{productId}/reviews",
        operationId: "getProductReviews",
        summary: "Get approved reviews for a specific product",
        tags: ["User - Review Module"],
        parameters: [
            new OA\Parameter(name: "productId", in: "path", required: true, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "sort_by", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["id", "created_at", "rating"])),
            new OA\Parameter(name: "sort_direction", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["asc", "desc"])),
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of approved product reviews",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "integer", example: 200),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/ReviewResource")
                        ),
                        new OA\Property(property: "meta", type: "object"),
                        new OA\Property(property: "links", type: "object")
                    ]
                )
            )
        ]
    )]
    public function getByProduct(int $productId, ReviewFilterRequest $request): JsonResponse
    {
        $reviews = $this->reviewService->getApprovedByProduct($productId, ReviewFilterDTO::fromRequest($request));

        return $this->paginatedResponse(ReviewResource::collection($reviews));
    }
}
