<?php

namespace App\Contracts;

interface CategoryServiceInterface
{
    /**
     * Lấy danh sách dữ liệu (hỗ trợ phân trang & lọc)
     */
    public function getAll(int $perPage = 15);

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(int $id);

    /**
     * Tạo mới dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: CreateCategoryServiceInterfaceDTO $dto)
     */
    public function create(object $dto);

    /**
     * Cập nhật dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: UpdateCategoryServiceInterfaceDTO $dto)
     */
    public function update(object $dto, int $id);

    /**
     * Xóa bản ghi
     */
    public function delete(int $id);
}