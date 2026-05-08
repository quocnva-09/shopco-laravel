<?php

namespace App\Contracts;

use App\DTOs\CategoryDTO;
use App\DTOs\CategoryFilterDTO;

interface CategoryServiceInterface
{
    /**
     * Lấy danh sách dữ liệu (hỗ trợ phân trang & lọc)
     */
    public function getAll(CategoryFilterDTO $filter);

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(int $id);

    /**
     * Tạo mới dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: CreateCategoryServiceInterfaceDTO $dto)
     */
    public function create(CategoryDTO $dto);

    /**
     * Cập nhật dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: UpdateCategoryServiceInterfaceDTO $dto)
     */
    public function update(int $id, CategoryDTO $dto);

    /**
     * Xóa bản ghi
     */
    public function delete(int $id);

    /**
     * Lấy danh sách bản ghi đã xóa
     */
    public function getTrashed(CategoryFilterDTO $filter);

    /**
     * Khôi phục bản ghi đã xóa
     */
    public function restore(int $id);

    /**
     * Xóa vĩnh viễn bản ghi
     */
    public function forceDelete(int $id);
}
