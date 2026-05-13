<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Product\ProductDTO;
use App\DTOs\Product\ProductFilterDTO;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function getAll(ProductFilterDTO $filter): LengthAwarePaginator;

    public function findById(int $id): Product;

    public function create(ProductDTO $dto): Product;

    public function update(ProductDTO $dto, int $id): Product;

    public function delete(int $id): void;

    public function getTrashed(ProductFilterDTO $filter): LengthAwarePaginator;

    public function restore(int $id): Product;

    public function forceDelete(int $id): void;
}
