<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\DTOs\Category\CategoryDTO;
use App\DTOs\Category\CategoryFilterDTO;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $repo,
    ) {
    }

    public function getAll(CategoryFilterDTO $filter): LengthAwarePaginator
    {
        return $this->repo->paginateAll($filter);
    }

    public function findById(int $id): Category
    {
        return $this->repo->findById($id);
    }

    public function create(CategoryDTO $dto): Category
    {
        return $this->repo->create($dto->toArray());
    }

    public function update(CategoryDTO $dto, int $id): Category
    {
        $category = $this->repo->findById($id);

        return $this->repo->update($category, $dto->toArray());
    }

    public function delete(int $id): Category
    {
        $category = $this->repo->findById($id);
        $this->repo->softDelete($category);

        return $category;
    }

    public function getTrashed(CategoryFilterDTO $filter): LengthAwarePaginator
    {
        return $this->repo->paginateTrashed($filter);
    }

    public function restore(int $id): Category
    {
        $category = $this->repo->findTrashedById($id);

        return $this->repo->restore($category);
    }

    public function forceDelete(int $id): Category
    {
        $category = $this->repo->findTrashedById($id);

        return $this->repo->forceDelete($category);
    }
}
