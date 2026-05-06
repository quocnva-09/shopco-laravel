<?php

namespace App\Contracts;

use App\Models\Category;
use App\DTOs\CategoryDTO;

interface CategoryServiceInterface
{
    /**
     * Lấy danh sách dữ liệu (hỗ trợ phân trang & lọc)
     */
    public function getAll(int $perPage = 15);

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(Category $category);

    /**
     * Tạo mới dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: CreateCategoryServiceInterfaceDTO $dto)
     */
    public function create(CategoryDTO $dto);

    /**
     * Cập nhật dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: UpdateCategoryServiceInterfaceDTO $dto)
     */
    public function update(CategoryDTO $dto, Category $category);

    /**
     * Xóa bản ghi
     */
    public function delete(Category $category);
}