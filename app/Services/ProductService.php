<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\DTOs\Product\ProductDTO;
use App\DTOs\Product\ProductFilterDTO;
use App\Enums\CacheConstants;
use App\Helpers\CacheHelper;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Exception;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        protected readonly ProductRepositoryInterface $repo,
        protected readonly FileUploadService $fileUploadService,
    ) {
    }

    public function getAll(ProductFilterDTO $filter): LengthAwarePaginator
    {
        if ($filter->categoryId !== null || $filter->categorySlug !== null) {
            $category = null;
            if ($filter->categoryId !== null) {
                $category = Category::with('children')->find($filter->categoryId);
            } elseif ($filter->categorySlug !== null) {
                $category = Category::with('children')->where('slug', $filter->categorySlug)->first();
            }

            if ($category) {
                $categoryIds = [$category->id];
                if ($category->children->isNotEmpty()) {
                    $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
                }
                $filter = $filter->withCategoryIds($categoryIds);
            }
        }

        $cacheKey = 'products_list' . $filter->toCacheKey();

        // Use manual serialize/unserialize to bypass igbinary serialization issues in Redis
        $cachedDataString = CacheHelper::rememberWithTags(
            [CacheConstants::PRODUCT_TAGS->value],
            $cacheKey,
            CacheConstants::CACHE_TTL,
            function () use ($filter) {
                return serialize($this->repo->paginateAll($filter));
            }
        );

        return unserialize($cachedDataString);
    }

    public function findById(int $id): Product
    {
        return $this->repo->findById($id);
    }

    public function create(ProductDTO $dto): Product
    {
        $data = $dto->toArray();

        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product = $this->repo->create($data);

        if ($dto->images !== null && !empty($dto->images)) {
            $this->addImages($product, $dto->images);
        }

        return $product->loadMissing(['category', 'images']);
    }

    public function update(ProductDTO $dto, int $id): Product
    {
        $product = $this->repo->findById($id);
        $data = $dto->toArray();

        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product = $this->repo->update($product, $data);

        if ($dto->images !== null) {
            $this->syncImages($product, $dto->images);
        }

        return $product->loadMissing(['category', 'images']);
    }

    public function delete(int $id): void
    {
        $product = $this->repo->findById($id);
        $this->repo->softDelete($product);
    }

    public function getTrashed(ProductFilterDTO $filter): LengthAwarePaginator
    {
        return $this->repo->paginateTrashed($filter);
    }

    public function restore(int $id): Product
    {
        $product = $this->repo->findTrashedById($id);

        return $this->repo->restore($product);
    }

    public function forceDelete(int $id): void
    {
        $product = Product::withTrashed()->with('images')->findOrFail($id);
        $this->repo->forceDelete($product);
    }


    public function syncImages(Product $product, array $imageUrls): void
    {
        $existingImages = $product->images()->pluck('img_path')->toArray();

        $imagesToDelete = array_diff($existingImages, $imageUrls);
        if (!empty($imagesToDelete)) {
            $this->deleteImages($product, $imagesToDelete);
        }

        $imagesToAdd = array_diff($imageUrls, $existingImages);
        if (!empty($imagesToAdd)) {
            $this->addImages($product, array_values($imagesToAdd));
        }
    }

    public function addImages(Product $product, array $imageUrls): void
    {
        $currentImageCount = $product->images()->count();

        foreach ($imageUrls as $index => $path) {
            $this->repo->addImage($product, [
                'img_path' => $path,
                'alt' => $product->name . ' - ' . ($currentImageCount + $index + 1),
                'is_primary' => $index === 0 && $currentImageCount === 0,
            ]);
        }
    }

    public function deleteImages(Product $product, array $imageUrls): void
    {
        if (empty($imageUrls)) {
            return;
        }

        $imagesToDelete = $product->images()->whereIn('img_path', $imageUrls)->get();

        foreach ($imagesToDelete as $image) {
            $this->fileUploadService->delete($image->img_path);
            $image->delete();
        }
    }
}
