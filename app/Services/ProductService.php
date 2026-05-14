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
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        protected readonly ProductRepositoryInterface $repo,
    ) {
    }

    public function getAll(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $cacheKey = 'products_list' . $filter->toCacheKey();

        // Sử dụng serialize/unserialize thủ công để vượt qua lỗi của igbinary trong Redis
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

        if (!empty($dto->images)) {
            $this->uploadImages($product, $dto->images);
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

        if (!empty($dto->images)) {
            $this->uploadImages($product, $dto->images);
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

    /**
     * Handle file storage and delegate image record creation to the repository.
     *
     * @param  UploadedFile[]  $images
     */
    private function uploadImages(Product $product, array $images): void
    {
        $currentImageCount = $product->images()->count();

        foreach ($images as $index => $image) {
            $extension = $image->getClientOriginalExtension();
            $fileName = $product->id . '_' . time() . '_' . $index . '.' . $extension;
            $path = $image->storeAs('products', $fileName);

            $this->repo->addImage($product, [
                'img_path' => $path,
                'alt' => $product->name . ' - ' . $index,
                'is_primary' => $index === 0 && $currentImageCount === 0,
            ]);
        }
    }
}
