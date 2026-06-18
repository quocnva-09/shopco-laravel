<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ColorServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\ColorResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends Controller
{
    public function __construct(
        private readonly ColorServiceInterface $colorService
    ) {
    }

    #[OA\Get(
        path: '/api/colors',
        summary: 'List colors (public)',
        tags: ['Master Data'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Colors retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Colors retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ColorResource')
                        )
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $colors = $this->colorService->getAll();

        return $this->successResponse(ColorResource::collection($colors), __('response.color.list_retrieved'));
    }
}
