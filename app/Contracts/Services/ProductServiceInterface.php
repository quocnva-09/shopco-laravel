<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\ProductDTO;
use App\DTOs\ProductFilterDTO;

interface ProductServiceInterface
{
    /**
     * Lấy danh sách Product
     */
    public function getAll(ProductFilterDTO $filter);

    /**
     * Lấy chi tiết Product
     */
    public function findById(int $id);

    /**
     * Tạo mới Product
     */
    public function create(ProductDTO $dto);

    /**
     * Cập nhật Product
     */
    public function update(ProductDTO $dto, int $id);

    /**
     * Xóa Product
     */
    public function delete(int $id);
}
