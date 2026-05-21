<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ExportServiceInterface;
use App\DTOs\Export\ExportDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Export\ExportRequest;
use App\Http\Resources\ExportResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    use ApiResponseTrait;

    private ExportServiceInterface $exportService;

    public function __construct(ExportServiceInterface $exportService)
    {
        $this->exportService = $exportService;
    }

    #[OA\Post(
        path: '/api/admin/exports/products',
        summary: 'Trigger a new product export job',
        security: [['bearerAuth' => []]],
        tags: ['Export Module - Admin'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ExportRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_ACCEPTED,
                description: 'Export processing started successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_ACCEPTED),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Export processing started successfully.'
                        ),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ExportResource'),
                    ]
                )
            ),
        ]
    )]
    public function exportProduct(ExportRequest $request): JsonResponse
    {
        $dto = ExportDTO::fromRequest($request->validated());

        $exportHistory = $this->exportService->requestProductExport($dto);

        return $this->successResponse(
            new ExportResource($exportHistory),
            __('response.export.initialized'),
            Response::HTTP_ACCEPTED
        );
    }

    #[OA\Get(
        path: '/api/admin/exports',
        summary: 'List the current admin\'s export history',
        security: [['bearerAuth' => []]],
        tags: ['Export Module - Admin'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Export history retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ExportResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta'),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $histories = $this->exportService->getUserExportHistories();

        return $this->paginatedResponse(ExportResource::collection($histories), __('response.export.list_retrieved'));
    }

    #[OA\Get(
        path: '/api/admin/exports/{id}',
        summary: 'Get a single export job record',
        security: [['bearerAuth' => []]],
        tags: ['Export Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Export record retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'success'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ExportResource'),
                    ]
                )
            ),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $exportHistory = $this->exportService->getUserExportHistory($id);

        return $this->successResponse(new ExportResource($exportHistory), __('response.export.retrieved'));
    }

    #[OA\Get(
        path: '/api/admin/exports/{id}/download',
        summary: 'Get the S3 download URL for a completed export',
        security: [['bearerAuth' => []]],
        tags: ['Export Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Download URL retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'download_url', type: 'string', format: 'uri'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Export not ready or file not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_BAD_REQUEST),
                        new OA\Property(property: 'message', type: 'string', example: 'Export file is not ready yet'),
                    ]
                )
            ),
        ]
    )]
    public function download(int $id): JsonResponse
    {
        try {
            $url = $this->exportService->getProductExportUrl($id);

            return $this->successResponse(['download_url' => $url], __('response.export.retrieved'));
        } catch (\LogicException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
