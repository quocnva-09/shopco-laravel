<?php

namespace App\Services;

use App\Contracts\ProductServiceInterface;

class ProductService implements ProductServiceInterface
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
    public function getAll(array $filters = [], int $perPage = 15)
    {
        // TODO: Implement getAll() method.
    }

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(int $id)
    {
        // TODO: Implement findById() method.
    }

    /**
     * Tạo mới dữ liệu từ DTO
     */
    public function create(object $dto)
    {
        // TODO: Implement create() method.
    }

    /**
     * Cập nhật dữ liệu từ DTO
     */
    public function update($model, object $dto)
    {
        // TODO: Implement update() method.
    }

    /**
     * Xóa bản ghi
     */
    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }
}