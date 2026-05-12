<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\ProductServiceInterface;
use App\DTOs\Product\ProductDTO;
use App\DTOs\Product\ProductFilterDTO;
use App\Enums\FilterEnum;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    public function getAll(ProductFilterDTO $filter)
    {
        $query = Product::with(['category', 'images']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%'.$filter->search.'%')
                  ->orWhere('description', 'like', '%'.$filter->search.'%');
            });
        }

        if (! empty($filter->categoryId)) {
            $query->where('category_id', $filter->categoryId);
        }

        if (in_array($filter->sortBy, FilterEnum::PRODUCT_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION) ? $filter->sortDir : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function findById(int $id)
    {
        return Product::findOrFail($id)->loadMissing(['category', 'images']);
    }

    public function create(ProductDTO $dto)
    {
        $data = $dto->toArray();

        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product = Product::create($data);

        if (! empty($dto->images)) {
            $this->uploadImages($product, $dto->images);
        }

        return $product->loadMissing(['category', 'images']);
    }

    public function update(ProductDTO $dto, int $id)
    {
        $product = $this->findById($id);
        $data = $dto->toArray();

        if (isset($data['name']) && empty($data['slug']) && empty($product->slug)) {
            $data['slug'] = Str::slug($data['name']);
        } elseif (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);

        if (! empty($dto->images)) {
            $this->uploadImages($product, $dto->images);
        }

        return $product->loadMissing(['category', 'images']);
    }

    public function delete(int $id)
    {
        $product = $this->findById($id);
        $product->delete();
    }

    private function uploadImages(Product $product, array $images): void
    {
        $currentImageCount = $product->images()->count();

        foreach ($images as $index => $image) {
            $extension = $image->getClientOriginalExtension();

            $fileName = $product->id.'_'.time().'_'.$index.'.'.$extension;

            $path = $image->storeAs('products', $fileName, 'public');

            $product->images()->create([
                'img_path' => $path,
                'alt' => $product->name.' - '.$index,
                'is_primary' => $index === 0 && $currentImageCount === 0,
            ]);
        }
    }

    public function getTrashed(ProductFilterDTO $filter)
    {
        $query = Product::onlyTrashed()->with(['category', 'images']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%'.$filter->search.'%')
                  ->orWhere('description', 'like', '%'.$filter->search.'%');
            });
        }

        if (! empty($filter->categoryId)) {
            $query->where('category_id', $filter->categoryId);
        }

        if (in_array($filter->sortBy, FilterEnum::PRODUCT_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION) ? $filter->sortDir : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function restore(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return $product->loadMissing(['category', 'images']);
    }

    public function forceDelete(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();
    }
}
