<?php

namespace App\Services;

use App\Contracts\Services\CategoryServiceInterface;
use App\DTOs\Category\CategoryFilterDTO;
use App\Enums\FilterEnum;
use App\Models\Category;

class CategoryService implements CategoryServiceInterface
{
    /**
     * Khởi tạo class
     */
    public function __construct()
    {
        // Inject repository hoặc các dependency khác vào đây
    }

    /**
     * Lấy danh sách dữ liệu (hềEtrợ phân trang & lọc)
     */
    public function getAll(CategoryFilterDTO $filter)
    {
        $query = Category::query();

        if ($filter->search) {
            $query->where('name', 'like', '%'.$filter->search.'%')
                ->orWhere('description', 'like', '%'.$filter->search.'%');
        }

        if (in_array($filter->sortBy, FilterEnum::CATEGORY_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION) ? strtolower($filter->sortDir) : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    /**
     * Lấy chi tiết một bản ghi theo ID
     */
    public function findById(int $id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Tạo mới dữ liệu từ DTO
     */
    public function create(object $dto)
    {
        return Category::create($dto->toArray());
    }

    /**
     * Cập nhật dữ liệu từ DTO
     */
    public function update(int $id, object $dto)
    {
        $category = $this->findById($id);
        $category->update($dto->toArray());

        return $category;
    }

    /**
     * Xóa bản ghi
     */
    public function delete(int $id)
    {
        $category = $this->findById($id);
        $category->delete();

        return $category;
    }

    /**
     * Lấy danh sách bản ghi đã xóa
     */
    public function getTrashed(CategoryFilterDTO $filter)
    {
        $query = Category::onlyTrashed();

        if ($filter->search) {
            $query->where('name', 'like', '%'.$filter->search.'%')
                ->orWhere('description', 'like', '%'.$filter->search.'%');
        }

        if (in_array($filter->sortBy, FilterEnum::CATEGORY_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION) ? strtolower($filter->sortDir) : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    /**
     * Khôi phục bản ghi đã xóa
     */
    public function restore(int $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return $category;
    }

    /**
     * Xóa vĩnh viềE bản ghi
     */
    public function forceDelete(int $id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();

        return $category;
    }
}
