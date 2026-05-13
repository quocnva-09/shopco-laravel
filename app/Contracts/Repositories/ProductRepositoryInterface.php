<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTOs\Product\ProductFilterDTO;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginateAll(ProductFilterDTO $filter): LengthAwarePaginator;

    public function findById(int $id): Product;

    public function findTrashedById(int $id): Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function softDelete(Product $product): void;

    public function paginateTrashed(ProductFilterDTO $filter): LengthAwarePaginator;

    public function restore(Product $product): Product;

    public function forceDelete(Product $product): void;

    public function addImage(Product $product, array $imageData): void;
}
