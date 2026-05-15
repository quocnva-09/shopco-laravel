<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\TemplateServiceInterface;
use App\DTOs\TemplateDTO;
use App\DTOs\TemplateFilterDTO;
use App\Http\Requests\TemplateFilterRequest;
use App\Http\Requests\TemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\Template;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TemplateController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly TemplateServiceInterface $templateService
    ) {}

    #[OA\Get(
        path: '/api/admin/templates',
        summary: 'List templates',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Templates retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Templates retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/TemplateResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta')
                    ]
                )
            )
        ]
    )]
    public function index(TemplateFilterRequest $request): JsonResponse
    {
        $dto = TemplateFilterDTO::fromRequest($request);
        $templates = $this->templateService->list($dto);

        return $this->paginatedResponse(TemplateResource::collection($templates));
    }

    #[OA\Post(
        path: '/api/admin/templates',
        summary: 'Create a new template',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TemplateRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Created successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/TemplateResource')
                    ]
                )
            )
        ]
    )]
    public function store(TemplateRequest $request): JsonResponse
    {
        $dto = TemplateDTO::fromRequest($request);
        $template = $this->templateService->create($dto);

        return $this->successResponse(new TemplateResource($template), 'Created successfully', 201);
    }

    #[OA\Get(
        path: '/api/admin/templates/{id}',
        summary: 'Get a single template',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Template retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Template retrieved successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/TemplateResource')
                    ]
                )
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $template = $this->templateService->findById($id);

        return $this->successResponse(new TemplateResource($template));
    }

    #[OA\Put(
        path: '/api/admin/templates/{id}',
        summary: 'Update a template',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TemplateRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/TemplateResource')
                    ]
                )
            )
        ]
    )]
    public function update(TemplateRequest $request, Template $template): JsonResponse
    {
        $dto = TemplateDTO::fromRequest($request);
        $updatedTemplate = $this->templateService->update($template, $dto);

        return $this->successResponse(new TemplateResource($updatedTemplate), 'Updated successfully');
    }

    #[OA\Delete(
        path: '/api/admin/templates/{id}',
        summary: 'Delete a template',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Deleted successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function destroy(Template $template): JsonResponse
    {
        $this->templateService->delete($template);

        return $this->successResponse(null, 'Deleted successfully', 204);
    }

    // Optional Soft Delete endpoints (only if routes exist)

    #[OA\Get(
        path: '/api/admin/templates/trashed',
        summary: 'List trashed templates',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Trashed templates retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Trashed templates retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/TemplateResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta')
                    ]
                )
            )
        ]
    )]
    public function trashed(TemplateFilterRequest $request): JsonResponse
    {
        $dto = TemplateFilterDTO::fromRequest($request);
        $templates = $this->templateService->trashed($dto);

        return $this->paginatedResponse(TemplateResource::collection($templates));
    }

    #[OA\Patch(
        path: '/api/admin/templates/{id}/restore',
        summary: 'Restore a trashed template',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Restored successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Restored successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/TemplateResource')
                    ]
                )
            )
        ]
    )]
    public function restore(int $id): JsonResponse
    {
        $template = $this->templateService->restore($id);

        return $this->successResponse(new TemplateResource($template), 'Restored successfully');
    }

    #[OA\Delete(
        path: '/api/admin/templates/{id}/force-delete',
        summary: 'Permanently delete a template',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Template Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permanently deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Permanently deleted'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function forceDelete(int $id): JsonResponse
    {
        $this->templateService->forceDelete($id);

        return $this->successResponse(null, 'Permanently deleted', 204);
    }
}
