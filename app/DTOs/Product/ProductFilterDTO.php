<?php

namespace App\DTOs\Product;

use App\Enums\FilterEnum;
use Illuminate\Foundation\Http\FormRequest;

readonly class ProductFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public ?string $categorySlug = null,
        public ?array $categoryIds = null,
        public ?array $colors = null,
        public ?array $sizes = null,
        public ?array $styleIds = null,
        public ?array $styleSlugs = null,
        public ?float $minPrice = null,
        public ?int $maxPrice = null,
        public int $page = 1,
        public int $perPage = 15,
        public string $sortBy = 'created_at',
        public string $sortDir = 'desc',
    ) {
    }

    /**
     * Initialise the DTO from a Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            search: $validated['search'] ?? null,
            categoryId: isset($validated['category_id']) ? (int) $validated['category_id'] : null,
            categorySlug: $validated['category_slug'] ?? null,
            categoryIds: null,
            colors: $validated['colors'] ?? null,
            sizes: $validated['sizes'] ?? null,
            styleIds: $validated['style_ids'] ?? null,
            styleSlugs: $validated['style_slugs'] ?? null,
            minPrice: isset($validated['min_price']) ? (float) $validated['min_price'] : null,
            maxPrice: isset($validated['max_price']) ? (int) $validated['max_price'] : null,
            page: $validated['page'] ?? 1,
            perPage: $validated['per_page'] ?? 15,
            sortBy: $validated['sort_by'] ?? FilterEnum::PRODUCT_SORT[0],
            sortDir: $validated['sort_dir'] ?? FilterEnum::DIRECTION[0],
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'category_id' => $this->categoryId,
            'category_slug' => $this->categorySlug,
            'category_ids' => $this->categoryIds,
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'style_ids' => $this->styleIds,
            'style_slugs' => $this->styleSlugs,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDir,
        ];
    }

    public function toCacheKey(): string
    {
        return md5(json_encode([
            'search' => $this->search,
            'category_id' => $this->categoryId,
            'category_slug' => $this->categorySlug,
            'category_ids' => $this->categoryIds,
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'style_ids' => $this->styleIds,
            'style_slugs' => $this->styleSlugs,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDir,
        ]));
    }

    public function withCategoryIds(array $categoryIds): self
    {
        return new self(
            search: $this->search,
            categoryId: $this->categoryId,
            categorySlug: $this->categorySlug,
            categoryIds: $categoryIds,
            colors: $this->colors,
            sizes: $this->sizes,
            styleIds: $this->styleIds,
            styleSlugs: $this->styleSlugs,
            minPrice: $this->minPrice,
            maxPrice: $this->maxPrice,
            page: $this->page,
            perPage: $this->perPage,
            sortBy: $this->sortBy,
            sortDir: $this->sortDir,
        );
    }
}
