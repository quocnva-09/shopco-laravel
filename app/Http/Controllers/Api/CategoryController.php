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
    public function index(CategoryFilterRequest $request): JsonResponse
    {
        $filter = CategoryFilterDTO::fromRequest($request);
        $categories = $this->categoryService->getAll($filter);

        return $this->paginatedResponse(CategoryResource::collection($categories), 'Categories retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $data = CategoryDTO::fromRequest($request);
        $category = $this->categoryService->create($data);

        return $this->successResponse(new CategoryResource($category), 'Category created successfully', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        return $this->successResponse(new CategoryResource($category), 'Category retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $data = CategoryDTO::fromRequest($request);
        $category = $this->categoryService->update($id, $data);

        return $this->successResponse(new CategoryResource($category), 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->delete($id);

        return $this->successResponse(new CategoryResource($category), 'Category deleted successfully');
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(CategoryFilterRequest $request): JsonResponse
    {
        $filter = CategoryFilterDTO::fromRequest($request);
        $categories = $this->categoryService->getTrashed($filter);

        return $this->paginatedResponse(CategoryResource::collection($categories), 'Trashed categories retrieved successfully');
    }

    /**
     * Restore the specified trashed resource.
     */
    public function restore(int $id): JsonResponse
    {
        $category = $this->categoryService->restore($id);

        return $this->successResponse(new CategoryResource($category), 'Category restored successfully');
    }

    /**
     * Force delete the specified resource.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $this->categoryService->forceDelete($id);

        return $this->successResponse(null, 'Category permanently deleted successfully');
    }
}
