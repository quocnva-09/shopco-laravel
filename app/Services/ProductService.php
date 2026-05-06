<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProductServiceInterface;
use App\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    public function getAll(int $perPage = 15)
    {
        return Product::with(['category', 'images'])->simplePaginate($perPage);
    }

    public function findById(Product $product): Product
    {
        return $product->loadMissing(['category', 'images']);
    }

    public function create(ProductDTO $dto): Product
    {
        $data = $dto->toArray();
        
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product = Product::create($data);

        if (!empty($dto->images)) {
            $this->uploadImages($product, $dto->images);
        }

        return $product->loadMissing(['category', 'images']);
    }

    public function update(ProductDTO $dto, Product $product): Product
    {
        $data = $dto->toArray();

        if (isset($data['name']) && empty($data['slug']) && empty($product->slug)) {
            $data['slug'] = Str::slug($data['name']);
        } elseif (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);

        if (!empty($dto->images)) {
            $this->uploadImages($product, $dto->images);
        }

        return $product->loadMissing(['category', 'images']);
    }

    public function delete(Product $product): bool
    {
        return $product->delete() ?? false;
    }

    private function uploadImages(Product $product, array $images): void
    {
        $currentImageCount = $product->images()->count();

        foreach ($images as $index => $image) {
            $path = $image->store('products', 'public');
            
            $product->images()->create([
                'img_path' => $path,
                'alt' => $product->name,
                'is_primary' => $index === 0 && $currentImageCount === 0,
            ]);
        }
    }
}