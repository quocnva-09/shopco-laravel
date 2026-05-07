<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\DTOs\CategoryDTO;
use App\Http\Resources\CategoryResource;
use App\Contracts\CategoryServiceInterface;
use App\Http\Requests\CategoryFilterRequest;
use App\DTOs\CategoryFilterDTO;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    private CategoryServiceInterface $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CategoryFilterRequest $request)
    {
        $filter = CategoryFilterDTO::fromRequest($request);
        $categories = $this->categoryService->getAll($filter);

        $resource = CategoryResource::collection($categories)->response()->getData(true);

        return response()->json([
            'data' => $resource['data'],
            'meta' => $resource['meta'] ?? null,
            'links' => $resource['links'] ?? null,
            'message' => 'Categories retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $data = CategoryDTO::fromRequest($request);
        $category = $this->categoryService->create($data);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category created successfully',
            'status' => Response::HTTP_CREATED,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = $this->categoryService->findById($id);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, int $id)
    {
        $data = CategoryDTO::fromRequest($request);
        $category = $this->categoryService->update($id, $data);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category updated successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $category = $this->categoryService->delete($id);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category deleted successfully',
            'status' => Response::HTTP_OK,
        ]);
    }
}
