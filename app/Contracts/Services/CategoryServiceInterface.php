<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Category\CategoryDTO;
use App\DTOs\Category\CategoryFilterDTO;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServiceInterface
{
    public function getAll(CategoryFilterDTO $filter): LengthAwarePaginator;

    public function findById(int $id): Category;

    public function create(CategoryDTO $dto): Category;

    /**
     * Normalized argument order: DTO first, then ID (consistent with ProductServiceInterface).
     */
    public function update(CategoryDTO $dto, int $id): Category;

    public function delete(int $id): Category;

    public function getTrashed(CategoryFilterDTO $filter): LengthAwarePaginator;

    public function restore(int $id): Category;

    public function forceDelete(int $id): Category;
}
