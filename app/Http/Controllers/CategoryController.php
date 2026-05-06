<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\DTOs\CategoryDTO;
use App\Http\Resources\CategoryResource;
use App\Contracts\CategoryServiceInterface;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

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
    public function index()
    {

        $categories = $this->categoryService->getAll($perPage = 15);

        return response()->json([
            'data' => CategoryResource::collection($categories),
            'message' => 'Categories retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // TO FIX: FORM REQUEST THROW EXCEPTION WHEN CONTROLLER CALL THIS METHOD
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
    public function show(Category $category)
    {
        $category = $this->categoryService->findById($category->id);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category retrieved successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $data = CategoryDTO::fromRequest($request);
        $category = $this->categoryService->update($data, $category->id);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category updated successfully',
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category = $this->categoryService->delete($category->id);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category deleted successfully',
            'status' => Response::HTTP_OK,
        ]);
    }
}
