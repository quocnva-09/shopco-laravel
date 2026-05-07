<?php

namespace App\Services;

use App\Contracts\CategoryServiceInterface;
use App\Models\Category;

class CategoryService implements CategoryServiceInterface
{
    /**
     * Khởi tạo class
     */
    public function __construct()
    {
        // Inject repository hoặc các dependency khác vào đây
    }

    /**
     * Lấy danh sách dữ liệu (hỗ trợ phân trang & lọc)
     */
    public function getAll(int $perPage = 15)
    {
        return Category::simplePaginate($perPage);
    }

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(Category $category)
    {
        return $category;
    }

    /**
     * Tạo mới dữ liệu từ DTO
     */
    public function create(object $dto)
    {
        return Category::create([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
        ]);
    }

    /**
     * Cập nhật dữ liệu từ DTO
     */
    public function update(object $dto, Category $category)
    {
        $category->update([
            'name' => $dto->name,
            'slug' => $dto->slug,
            'description' => $dto->description,
        ]);

        return $category;
    }

    /**
     * Xóa bản ghi
     */
    public function delete(Category $category)
    {
        $category->delete();

        return $category;
    }
}