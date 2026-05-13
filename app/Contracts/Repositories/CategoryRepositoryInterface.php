<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTOs\Category\CategoryFilterDTO;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function paginateAll(CategoryFilterDTO $filter): LengthAwarePaginator;

    public function findById(int $id): Category;

    public function findTrashedById(int $id): Category;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function softDelete(Category $category): void;

    public function paginateTrashed(CategoryFilterDTO $filter): LengthAwarePaginator;

    public function restore(Category $category): Category;

    public function forceDelete(Category $category): Category;
}
