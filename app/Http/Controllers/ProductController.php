<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\ProductServiceInterface;
use App\DTOs\ProductDTO;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductFilterRequest;
use App\DTOs\ProductFilterDTO;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(ProductFilterRequest $request)
    {
        $dto = ProductFilterDTO::fromRequest($request);
        $products = $this->productService->getAll($dto);
        $resource = ProductResource::collection($products)->response()->getData(true);

        return response()->json([
            'data' => $resource['data'],
            'meta' => $resource['meta'] ?? null,
            'links' => $resource['links'] ?? null,
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

    public function show(int $id)
    {
        $product = $this->productService->findById($id);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function update(ProductRequest $request, int $id)
    {
        $dto = ProductDTO::fromRequest($request);

        $updatedProduct = $this->productService->update($dto, $id);

        return response()->json([
            'data' => new ProductResource($updatedProduct),
            'message' => 'Product updated successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    public function destroy(int $id)
    {
        $this->productService->delete($id);

        return response()->json([
            'message' => 'Product deleted successfully',
            'status' => Response::HTTP_OK,
        ]);
    }
}
