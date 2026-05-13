<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\DTOs\Category\CategoryFilterDTO;
use App\Enums\FilterEnum;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function paginateAll(CategoryFilterDTO $filter): LengthAwarePaginator
    {
        $query = Category::query();

        if ($filter->search) {
            $query->where('name', 'like', '%' . $filter->search . '%')
                ->orWhere('description', 'like', '%' . $filter->search . '%');
        }

        if (in_array($filter->sortBy, FilterEnum::CATEGORY_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? strtolower($filter->sortDir)
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function findById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function findTrashedById(int $id): Category
    {
        return Category::onlyTrashed()->findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function softDelete(Category $category): void
    {
        $category->delete();
    }

    public function paginateTrashed(CategoryFilterDTO $filter): LengthAwarePaginator
    {
        $query = Category::onlyTrashed();

        if ($filter->search) {
            $query->where('name', 'like', '%' . $filter->search . '%')
                ->orWhere('description', 'like', '%' . $filter->search . '%');
        }

        if (in_array($filter->sortBy, FilterEnum::CATEGORY_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? strtolower($filter->sortDir)
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function restore(Category $category): Category
    {
        $category->restore();

        return $category;
    }

    public function forceDelete(Category $category): Category
    {
        $category->forceDelete();

        return $category;
    }
}
