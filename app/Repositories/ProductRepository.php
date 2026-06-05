<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTOs\Product\ProductFilterDTO;
use App\Enums\FilterEnum;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginateAll(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $query = Product::with(['category', 'images', 'variants.color', 'variants.size'])
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
        return Product::with(['category', 'images', 'variants.color', 'variants.size'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->findOrFail($id);
    }

    public function findTrashedById(int $id): Product
    {
        return Product::onlyTrashed()->with(['category', 'images', 'variants.color', 'variants.size'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->findOrFail($id);
    }

    public function create(array $data): Product
    {
        $variants = $data['variants'] ?? [];
        unset($data['variants']);

        $product = Product::create($data);
        $this->syncVariants($product, $variants);

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $variants = $data['variants'] ?? null;
        unset($data['variants']);

        $product->update($data);

        if ($variants !== null) {
            $this->syncVariants($product, $variants);
        }

        return $product;
    }

    public function softDelete(Product $product): void
    {
        $product->delete();
    }

    public function paginateTrashed(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $query = Product::onlyTrashed()->with(['category', 'images', 'variants.color', 'variants.size'])
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

        return $product->loadMissing(['category', 'images', 'variants.color', 'variants.size'])
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
                $query->whereHas('variants.color', function ($q) use ($colors) {
                    $q->whereIn('name', $colors);
                });
            }
        }

        if (! empty($filter->sizes)) {
            $sizes = array_filter(array_map(function ($size) {
                return strtoupper(trim($size));
            }, explode(',', $filter->sizes)));

            if (! empty($sizes)) {
                $query->whereHas('variants.size', function ($q) use ($sizes) {
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

    /**
     * Sync ProductVariant records cho một product theo danh sách [{color_id, size_id}].
     * Tạo mới các variant chưa tồn tại, xóa các variant không còn trong danh sách.
     */
    private function syncVariants(Product $product, array $variants): void
    {
        // Build danh sách (color_id|size_id) cần giữ lại
        $incoming = collect($variants)->map(function (array $v): array {
            return [
                'color_id' => $v['color_id'] ?? null,
                'size_id'  => $v['size_id'] ?? null,
            ];
        });

        // Xóa các variant không còn trong danh sách mới
        $product->variants()->get()->each(function (ProductVariant $existing) use ($incoming): void {
            $stillNeeded = $incoming->contains(function (array $v) use ($existing): bool {
                return (int) $v['color_id'] === (int) $existing->color_id
                    && (int) $v['size_id']  === (int) $existing->size_id;
            });

            if (! $stillNeeded) {
                $existing->delete();
            }
        });

        // Tạo mới các variant chưa tồn tại
        foreach ($incoming as $v) {
            $product->variants()->firstOrCreate([
                'color_id' => $v['color_id'],
                'size_id'  => $v['size_id'],
            ]);
        }
    }
}
