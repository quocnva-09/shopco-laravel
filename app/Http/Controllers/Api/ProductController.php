<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ProductServiceInterface;
use App\DTOs\Product\ProductDTO;
use App\DTOs\Product\ProductFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductFilterRequest;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(ProductFilterRequest $request): JsonResponse
    {
        $dto = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getAll($dto);

        return $this->paginatedResponse(ProductResource::collection($products), 'Products retrieved successfully');
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromRequest($request);

        $product = $this->productService->create($dto);

        return $this->successResponse(new ProductResource($product), 'Product created successfully', Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        return $this->successResponse(new ProductResource($product), 'Product retrieved successfully');
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $dto = ProductDTO::fromRequest($request);

        $updatedProduct = $this->productService->update($dto, $id);

        return $this->successResponse(new ProductResource($updatedProduct), 'Product updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return $this->successResponse(null, 'Product deleted successfully');
    }

    public function trashed(ProductFilterRequest $request): JsonResponse
    {
        $dto = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getTrashed($dto);

        return $this->paginatedResponse(ProductResource::collection($products), 'Trashed products retrieved successfully');
    }

    public function restore(int $id): JsonResponse
    {
        $product = $this->productService->restore($id);

        return $this->successResponse(new ProductResource($product), 'Product restored successfully');
    }

    public function forceDelete(int $id): JsonResponse
    {
        $this->productService->forceDelete($id);

        return $this->successResponse(null, 'Product permanently deleted successfully');
    }
}
