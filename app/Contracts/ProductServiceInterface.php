<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\ProductDTO;
use App\Models\Product;

interface ProductServiceInterface
{
    /**
     * Lấy danh sách Product
     */
    public function getAll(int $perPage = 15);

    /**
     * Lấy chi tiết Product
     */
    public function findById(Product $product);

    /**
     * Tạo mới Product
     */
    public function create(ProductDTO $dto);

    /**
     * Cập nhật Product
     */
    public function update(ProductDTO $dto, Product $product);

    /**
     * Xóa Product
     */
    public function delete(Product $product);
}