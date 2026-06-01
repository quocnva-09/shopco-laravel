<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\CategoryServiceInterface;
use App\DTOs\Category\CategoryDTO;
use App\DTOs\Category\CategoryFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryFilterRequest;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryServiceInterface $categoryService
    ) {
    }

    #[OA\Get(
        path: '/api/admin/categories',
        summary: 'List categories',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Categories retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Categories retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/CategoryResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta')
                    ]
                )
            )
        ]
    )]
    public function index(CategoryFilterRequest $request): JsonResponse
    {
        $filter = CategoryFilterDTO::fromRequest($request);
        $categories = $this->categoryService->getAll($filter);

        return $this->paginatedResponse(CategoryResource::collection($categories), __('response.category.list_retrieved'));
    }

    #[OA\Post(
        path: '/api/admin/categories',
        summary: 'Create a new category',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CategoryRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Category created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_CREATED),
                        new OA\Property(property: 'message', type: 'string', example: 'Category created successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CategoryResource')
                    ]
                )
            )
        ]
    )]
    public function store(CategoryRequest $request): JsonResponse
    {
        $data = CategoryDTO::fromRequest($request);
        $category = $this->categoryService->create($data);

        return $this->successResponse(new CategoryResource($category), __('response.category.created'), Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/admin/categories/{id}',
        summary: 'Get a single category',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Category retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Category retrieved successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CategoryResource')
                    ]
                )
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        return $this->successResponse(new CategoryResource($category), __('response.category.retrieved'));
    }

    #[OA\Put(
        path: '/api/admin/categories/{id}',
        summary: 'Update a category',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CategoryRequest')
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Category updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Category updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CategoryResource')
                    ]
                )
            )
        ]
    )]
    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $data = CategoryDTO::fromRequest($request);
        $category = $this->categoryService->update($data, $id);

        return $this->successResponse(new CategoryResource($category), __('response.category.updated'));
    }

    #[OA\Delete(
        path: '/api/admin/categories/{id}',
        summary: 'Delete a category',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Category deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Category deleted successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->delete($id);

        return $this->successResponse(new CategoryResource($category), __('response.category.deleted'));
    }

    #[OA\Get(
        path: '/api/admin/categories/trashed',
        summary: 'List trashed categories',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Trashed categories retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Trashed categories retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/CategoryResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta')
                    ]
                )
            )
        ]
    )]
    public function trashed(CategoryFilterRequest $request): JsonResponse
    {
        $filter = CategoryFilterDTO::fromRequest($request);
        $categories = $this->categoryService->getTrashed($filter);

        return $this->paginatedResponse(CategoryResource::collection($categories), __('response.category.trashed_retrieved'));
    }

    #[OA\Patch(
        path: '/api/admin/categories/{id}/restore',
        summary: 'Restore a trashed category',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Category restored successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Category restored successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CategoryResource')
                    ]
                )
            )
        ]
    )]
    public function restore(int $id): JsonResponse
    {
        $category = $this->categoryService->restore($id);

        return $this->successResponse(new CategoryResource($category), __('response.category.restored'));
    }

    #[OA\Delete(
        path: '/api/admin/categories/{id}/force-delete',
        summary: 'Permanently delete a category',
        security: [['bearerAuth' => []]],
        tags: ['Category Module - Admin'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Category permanently deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: Response::HTTP_OK),
                        new OA\Property(property: 'message', type: 'string', example: 'Category permanently deleted successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
    public function forceDelete(int $id): JsonResponse
    {
        $this->categoryService->forceDelete($id);

        return $this->successResponse(null, __('response.category.force_deleted'));
    }
}
