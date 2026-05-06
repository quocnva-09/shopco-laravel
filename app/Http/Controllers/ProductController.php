<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\ProductServiceInterface;
use App\DTOs\ProductDTO;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    public function index()
    {
        $products = $this->productService->getAll();

        return response()->json([
            'data' => ProductResource::collection($products),
            'message' => 'Products retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function store(ProductRequest $request)
    {
        $dto = ProductDTO::fromRequest($request);

        $product = $this->productService->create($dto);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product created successfully',
            'status' => Response::HTTP_CREATED,
        ]);
    }

    public function show(Product $product)
    {
        $product = $this->productService->findById($product);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $dto = ProductDTO::fromRequest($request);

        $updatedProduct = $this->productService->update($dto, $product);

        return response()->json([
            'data' => new ProductResource($updatedProduct),
            'message' => 'Product updated successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return response()->json([
            'message' => 'Product deleted successfully',
            'status' => Response::HTTP_OK,
        ]);
    }
}
