<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTOs\Product\ProductFilterDTO;
use App\Enums\FilterEnum;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginateAll(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $query = Product::with(['category', 'images']);

        $this->applyFilters($query, $filter);

        if (in_array($filter->sortBy, FilterEnum::PRODUCT_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? $filter->sortDir
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function findById(int $id): Product
    {
        return Product::with(['category', 'images'])->findOrFail($id);
    }

    public function findTrashedById(int $id): Product
    {
        return Product::onlyTrashed()->with(['category', 'images'])->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function softDelete(Product $product): void
    {
        $product->delete();
    }

    public function paginateTrashed(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $query = Product::onlyTrashed()->with(['category', 'images']);

        $this->applyFilters($query, $filter);

        if (in_array($filter->sortBy, FilterEnum::PRODUCT_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? $filter->sortDir
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function restore(Product $product): Product
    {
        $product->restore();

        return $product->loadMissing(['category', 'images']);
    }

    public function forceDelete(Product $product): void
    {
        $product->forceDelete();
    }

    public function addImage(Product $product, array $imageData): void
    {
        $product->images()->create($imageData);
    }

    private function applyFilters($query, ProductFilterDTO $filter): void
    {
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter->search . '%')
                  ->orWhere('description', 'like', '%' . $filter->search . '%');
            });
        }

        if (! empty($filter->categoryId)) {
            $query->where('category_id', $filter->categoryId);
        }

        if (! empty($filter->colors)) {
            $rawColors = explode(',', $filter->colors);
            $colors = array_filter(array_map(function ($color) {
                return ucfirst(strtolower(trim($color)));
            }, $rawColors));
            
            if (! empty($colors)) {
                $query->where(function ($q) use ($colors) {
                    foreach ($colors as $color) {
                        $q->orWhereJsonContains('colors', $color);
                    }
                });
            }
        }

        if (! empty($filter->sizes)) {
            $sizes = array_filter(array_map('trim', explode(',', $filter->sizes)));
            if (! empty($sizes)) {
                $query->where(function ($q) use ($sizes) {
                    foreach ($sizes as $size) {
                        $q->orWhereJsonContains('sizes', $size);
                    }
                });
            }
        }

        if ($filter->minPrice !== null) {
            $query->whereRaw('COALESCE(price_discount, price) >= ?', [$filter->minPrice]);
        }

        if ($filter->maxPrice !== null) {
            $query->whereRaw('COALESCE(price_discount, price) <= ?', [$filter->maxPrice]);
        }
    }
}
