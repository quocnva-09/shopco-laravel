<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\StyleServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\StyleResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class StyleController extends Controller
{
    public function __construct(
        private readonly StyleServiceInterface $styleService
    ) {
    }

    #[OA\Get(
        path: '/api/styles',
        summary: 'List styles (public)',
        tags: ['Master Data'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Styles retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Styles retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/StyleResource')
                        )
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $styles = $this->styleService->getAll();

        return $this->successResponse(StyleResource::collection($styles), __('response.style.list_retrieved'));
    }
}
