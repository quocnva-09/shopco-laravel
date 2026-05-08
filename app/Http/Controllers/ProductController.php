<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\ProductServiceInterface;
use App\DTOs\ProductDTO;
use App\DTOs\ProductFilterDTO;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\ProductRequest;
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
}
