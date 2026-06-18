<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\SizeServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\SizeResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class SizeController extends Controller
{
    public function __construct(
        private readonly SizeServiceInterface $sizeService
    ) {
    }

    #[OA\Get(
        path: '/api/sizes',
        summary: 'List sizes (public)',
        tags: ['Master Data'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Sizes retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Sizes retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/SizeResource')
                        )
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $sizes = $this->sizeService->getAll();

        return $this->successResponse(SizeResource::collection($sizes), __('response.size.list_retrieved'));
    }
}
