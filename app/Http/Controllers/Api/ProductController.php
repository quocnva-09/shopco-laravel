<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ProductServiceInterface;
use App\DTOs\Product\ProductDTO;
use App\DTOs\Product\ProductFilterDTO;
use App\DTOs\Upload\FileUploadDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductFilterRequest;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Upload\FileUploadRequest;
use App\Http\Resources\ProductResource;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    // -------------------------------------------------------------------------
    // Public Routes
    // -------------------------------------------------------------------------

    #[OA\Get(
        path: '/api/products',
        summary: 'List products (public)',
        tags: ['User - Product Module'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Products retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ProductResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta'),
                    ]
                )
            ),
        ]
    )]
    #[OA\Get(
        path: '/api/admin/products',
        summary: 'List products (admin)',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Product Module'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Products retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ProductResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta'),
                    ]
                )
            ),
        ]
    )]
    public function index(ProductFilterRequest $request): JsonResponse
    {
        $dto = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getAll($dto);

        return $this->paginatedResponse(ProductResource::collection($products), 'Products retrieved successfully');
    }

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get a single product (public)',
        tags: ['User - Product Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Product retrieved successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductResource'),
                    ]
                )
            ),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        return $this->successResponse(new ProductResource($product), 'Product retrieved successfully');
    }

    // -------------------------------------------------------------------------
    // Admin Routes
    // -------------------------------------------------------------------------

    #[OA\Post(
        path: '/api/admin/products/upload',
        summary: 'Upload a product image',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['image'],
                    properties: [
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Product image file (max 5MB, jpeg/png/jpg/webp)'),
                    ]
                )
            )
        ),
        tags: ['Admin - Product Module'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Image uploaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Uploaded successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'img_path', type: 'string', example: 'products/xyz.jpg'),
                                new OA\Property(property: 'image_url', type: 'string', example: 'https://s3.amazonaws.com/bucket/products/xyz.jpg'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function uploadImage(FileUploadRequest $request): JsonResponse
    {
        $dto = FileUploadDTO::fromRequest($request);
        $path = $this->productService->uploadImage($dto->file, 'products');

        if (!$path) {
            return $this->errorResponse('Upload failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->successResponse([
            'img_path' => $path,
            'image_url' => app(FileUploadService::class)->url($path),
        ], 'Uploaded successfully');
    }

    #[OA\Post(
        path: '/api/admin/products',
        summary: 'Create a new product',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductRequest')
        ),
        tags: ['Admin - Product Module'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Product created successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductResource'),
                    ]
                )
            ),
        ]
    )]
    public function store(ProductRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $product = $this->productService->create($dto);

        return $this->successResponse(new ProductResource($product), 'Product created successfully', Response::HTTP_CREATED);
    }

    #[OA\Put(
        path: '/api/admin/products/{id}',
        summary: 'Update a product',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductRequest')
        ),
        tags: ['Admin - Product Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Product updated successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductResource'),
                    ]
                )
            ),
        ]
    )]
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $dto = ProductDTO::fromRequest($request);
        $updatedProduct = $this->productService->update($dto, $id);

        return $this->successResponse(new ProductResource($updatedProduct), 'Product updated successfully');
    }

    #[OA\Delete(
        path: '/api/admin/products/{id}',
        summary: 'Delete a product',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Product Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Product deleted successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return $this->successResponse(null, 'Product deleted successfully');
    }


    #[OA\Get(
        path: '/api/admin/products/trashed',
        summary: 'List trashed products',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Product Module'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'perPage', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_dir', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Trashed products retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Trashed products retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ProductResource')
                        ),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginatedMeta'),
                    ]
                )
            ),
        ]
    )]
    public function trashed(ProductFilterRequest $request): JsonResponse
    {
        $dto = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getTrashed($dto);

        return $this->paginatedResponse(ProductResource::collection($products), 'Trashed products retrieved successfully');
    }

    #[OA\Patch(
        path: '/api/admin/products/{id}/restore',
        summary: 'Restore a trashed product',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Product Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product restored successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Product restored successfully'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductResource'),
                    ]
                )
            ),
        ]
    )]
    public function restore(int $id): JsonResponse
    {
        $product = $this->productService->restore($id);

        return $this->successResponse(new ProductResource($product), 'Product restored successfully');
    }

    #[OA\Delete(
        path: '/api/admin/products/{id}/force-delete',
        summary: 'Permanently delete a product',
        security: [['bearerAuth' => []]],
        tags: ['Admin - Product Module'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product permanently deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Product permanently deleted successfully'),
                        new OA\Property(property: 'data', type: 'object', nullable: true, example: null),
                    ]
                )
            ),
        ]
    )]
    public function forceDelete(int $id): JsonResponse
    {
        $this->productService->forceDelete($id);

        return $this->successResponse(null, 'Product permanently deleted successfully');
    }
}
