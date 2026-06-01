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
        $query = Product::with(['category', 'images', 'colors', 'sizes'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews');

        $this->applyFilters($query, $filter);

        $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
            ? $filter->sortDir
            : 'desc';

        if ($filter->sortBy === 'selling') {
            $query->withSum(['orderItems as sold_count' => function ($q) {
                $q->join('orders', 'orders.id', '=', 'order_items.order_id')
                  ->where('orders.status', \App\Enums\OrderStatus::PAID->value);
            }], 'quantity')
            ->orderBy('sold_count', $direction);
        } elseif (in_array($filter->sortBy, FilterEnum::PRODUCT_SORT)) {
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function findById(int $id): Product
    {
        return Product::with(['category', 'images', 'colors', 'sizes'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->findOrFail($id);
    }

    public function findTrashedById(int $id): Product
    {
        return Product::onlyTrashed()->with(['category', 'images', 'colors', 'sizes'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->findOrFail($id);
    }

    public function create(array $data): Product
    {
        $colorIds = $data['color_ids'] ?? [];
        $sizeIds = $data['size_ids'] ?? [];
        unset($data['color_ids'], $data['size_ids']);

        $product = Product::create($data);
        $product->colors()->sync($colorIds);
        $product->sizes()->sync($sizeIds);

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $colorIds = $data['color_ids'] ?? null;
        $sizeIds = $data['size_ids'] ?? null;
        unset($data['color_ids'], $data['size_ids']);

        $product->update($data);

        if ($colorIds !== null) {
            $product->colors()->sync($colorIds);
        }
        if ($sizeIds !== null) {
            $product->sizes()->sync($sizeIds);
        }

        return $product;
    }

    public function softDelete(Product $product): void
    {
        $product->delete();
    }

    public function paginateTrashed(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $query = Product::onlyTrashed()->with(['category', 'images', 'colors', 'sizes'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews');

        $this->applyFilters($query, $filter);

        $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
            ? $filter->sortDir
            : 'desc';

        if ($filter->sortBy === 'selling') {
            $query->withSum(['orderItems as sold_count' => function ($q) {
                $q->join('orders', 'orders.id', '=', 'order_items.order_id')
                  ->where('orders.status', \App\Enums\OrderStatus::PAID->value);
            }], 'quantity')
            ->orderBy('sold_count', $direction);
        } elseif (in_array($filter->sortBy, FilterEnum::PRODUCT_SORT)) {
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function restore(Product $product): Product
    {
        $product->restore();

        return $product->loadMissing(['category', 'images', 'colors', 'sizes'])
            ->loadAvg('approvedReviews', 'rating')
            ->loadCount('approvedReviews');
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
                return strtolower(trim($color));
            }, $rawColors));
            
            if (! empty($colors)) {
                $query->whereHas('colors', function ($q) use ($colors) {
                    $q->whereIn('name', $colors);
                });
            }
        }

        if (! empty($filter->sizes)) {
            $sizes = array_filter(array_map(function ($size) {
                return strtoupper(trim($size));
            }, explode(',', $filter->sizes)));
            
            if (! empty($sizes)) {
                $query->whereHas('sizes', function ($q) use ($sizes) {
                    $q->whereIn('name', $sizes);
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
