<?php

namespace App\Contracts;

interface ProductServiceInterface
{
    /**
     * Lấy danh sách dữ liệu (hỗ trợ phân trang & lọc)
     */
    public function getAll(array $filters = [], int $perPage = 15);

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(int $id);

    /**
     * Tạo mới dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: CreateProductServiceInterfaceDTO $dto)
     */
    public function create(object $dto);

    /**
     * Cập nhật dữ liệu từ DTO
     * TODO: Gắn type-hint DTO cụ thể (VD: UpdateProductServiceInterfaceDTO $dto)
     */
    public function update($model, object $dto);

    /**
     * Xóa bản ghi
     */
    public function delete(int $id);
}